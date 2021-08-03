<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Favourites extends Model
{
    use HasFactory;

    protected $table = 'user_favourites';

    protected $fillable = ['user_id' , 'film_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
