<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aka extends Model
{
    use HasFactory;

    protected $fillable = [
        'tv_show_id',
        'name',
        'country', // Optional, if provided in API response
    ];

    public function tvShow()
    {
        return $this->belongsTo(TvShow::class);
    }
}
