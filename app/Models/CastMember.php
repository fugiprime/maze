<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CastMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function tvShows()
    {
        return $this->belongsToMany(TvShow::class, 'show_cast'); // Use 'show_cast' pivot table
    }
}
