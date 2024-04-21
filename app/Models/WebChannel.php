<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebChannel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function tvShows()
    {
        return $this->belongsToMany(TvShow::class, 'tv_show_web_channel'); // Web channel can have many TV shows
    }

    public function getRouteKeyName()
    {
        return 'id'; // or whichever attribute you want to use for binding
    }
}
