<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Http;
use Illuminate\Console\Command;
use App\Models\Film;
use Cache;
use Log;

class FilmsCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'films:upload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uploading Films to DB';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $api = 'https://api.themoviedb.org/3/movie/';
        //API KEY IS OPTIONAL FOR EACH USER, PLEASE PUT HERE YOUR API KEY WHEN YOU TESTING THIS!
        $api_key = env('MOVIE_DB_API_KEY');
        
        $moviescount = 4;
        for($i = 0; $i <= $moviescount ; $i++){
            $randomNum = rand(1, 2000000);
            $query = Http::get($api.$randomNum.'?api_key='.$api_key."&language=ru-RU")->json();
            
            if(isset($query['status_code']) || Film::where('title',  $query['title'])->exists()){
                $moviescount++;
            }
            else{
                $film = Film::create([
                    'title'=> $query['title'],
                    'poster_path' => $query['poster_path'],
                ]);
                
                Redis::hmset('film:'. $film->id, [
                        'id' => $film->id,
                        'title'=> $film->title,
                        'poster_path' => $film->poster_path,
                ]);
            
            }
        }
    }
}
