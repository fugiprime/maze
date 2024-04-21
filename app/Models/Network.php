<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Network extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function tvShows()
    {
        return $this->belongsToMany(TVShow::class, 'tv_show_network');
    }


    public function getRouteKeyName()
    {
        return 'id'; // or whichever attribute you want to use for binding
    }
}
