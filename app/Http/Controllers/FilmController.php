<?php

namespace App\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Redis;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use App\Models\Favourites;
use App\Models\Film;
use App\Models\User;
use Validator;

use Cache;

class FilmController extends Controller
{
    
    public function filmsAll(){
        
        $films  = Collection::make([Film::paginate(15)])
        ->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return  $films;
    }
    public function AddToFavourites(Request $request){
        
        $user = $request->user();
        $data = $request->all();
        $data['user_id'] = $user->id;
        
       
        $validator = Validator::make($data,[
            'film_id' => 'required',
        ]);
        
 
      
        if ($validator->fails()) {
            
            return response()->json([$validator->messages(), 'status' => 500], 200);
        }
       
        if(Favourites::where('user_id', $data['user_id'])->where('film_id', $data['film_id'])){
            
            return response()->json([
               'error' => 'Film Is Already In Your Favourites',
                'code' => 500,
           ]);
        }  
        
        if(is_null(Film::where('id', $data['film_id']))){
          
            return response()->json([
                'error' => 'There Is No Film With That Id',
                 'code' => 500,
            ]);
        }

        Favourites::create($data);

        return response()->json([
            'message' => 'Film Added To Favourites',
        ], 200);
    }   

    public function deleteFromFavourites(Request $request){
        $user = $request->user();
        $data = $request->all();
        $data['user_id'] = $user->id;

        $validator = Validator::make($data,[
            'film_id' => 'required',
        ]);

        if ($validator->fails()) {
            
            return response()->json([$validator->messages(), 'status' => 500], 200);
        }


        $favourite = Favourites::where('user_id', $data['user_id'])
                     ->where('film_id', $data['film_id'])->first();

        if(is_null($favourite)){
            return response()->json([
                'error' => 'Film is Not In Your Favourites',
                'code' => 500,
            ]);
        }    

        $favourite->delete();
        
        return response()->json([
            'message' => 'Film Deleted From Favourites',
        ], 200);
    }


    public function NotInFavourites(Request $request)
    {
        $user = $request->user();
        $data = $request->all();
        $favourites = $user->favourites;
        $favouritesId = $user->favourites->pluck('film_id')->toArray();
     
        $validator = Validator::make($data,[
            'loader_type' => 'required',
        ]);

       
        if ($validator->fails()) {
            
            return response()->json([$validator->messages(), 'status' => 500], 200);
        }

        if($data['loader_type'] == 0){
            $films =  Film::whereNotIn('id', $favourites)->get();

            return  $films->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    
        if($data['loader_type'] == 1){
            $key_prefix = 'laravel_database_film:';
            $films = [];
            
            foreach(Redis::keys("film:*") as $item){
                $films[] =Redis::hgetall('film:'.substr($item, strlen($key_prefix)));
            }

            $collection = collect($films);
            
            $filtered_collection = $collection->filter(function ($item)  use($favouritesId){
             
                return !in_array($item['id'], $favouritesId);
            });
        }
        
        return  $filtered_collection->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        
    }   
}
