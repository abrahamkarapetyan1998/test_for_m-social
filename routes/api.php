<?php

use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use  Illuminate\Support\Facades\Http;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\UserController;
use App\Models\Film;
use App\Models\User;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('/foo', function(Request $request){
     $user  = $request->user();
    $key_prefix = 'laravel_database_film:';
     $films = [];
  
    



    foreach(Redis::keys("film:*") as $item){
        $films[] = collect(Redis::hgetall('film:'.substr($item, strlen($key_prefix))));
    }
   
     

    $diff = $collection->diffAssoc($collection2);
 
    
});
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/me', [ UserController::class, 'Me'])->middleware('auth:sanctum');
Route::post('/edit_user', [UserController::class, 'EditUser'])->middleware('auth:sanctum');
Route::post('/delete_user', [UserController::class, 'DeleteUser'])->middleware('auth:sanctum');
Route::get('/films_all' , [FilmController::class, 'filmsAll']);
Route::get('/films_not_in_favourites' ,[FilmController::class, 'NotInFavourites'])->middleware('auth:sanctum');
Route::post('/add_to_favourites' , [FilmController::class, 'AddToFavourites'])->middleware('auth:sanctum');
Route::post('/delete_from_favourites', [FilmController::class, 'deleteFromFavourites'])->middleware('auth:sanctum');
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

