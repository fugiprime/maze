<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TvShow extends Model
{
    use HasFactory;

    protected $fillable = [
        'maze_id', // Unique ID from the external source
        'url', // URL of the show on the external source
        'name',
        'type',
        'language', // Optional, language of the show
        'status', // Optional, current status of the show (e.g., running, ended)
        'runtime', // Optional, episode runtime in minutes
        'premiered', // Optional, premiered date
        'official_site', // Optional, official website URL
        'rating', // Optional, rating (decimal value)
        'summary', // Optional, summary of the show
        'imdb_id', // Optional, IMDB ID
        'image_url', // URL of the show image
        'slug',
    ];

    public function networks() // Renamed for clarity (plural)
    {
        return $this->belongsToMany(Network::class, 'tv_show_network'); // Use 'show_networks' pivot table
    }

    public function webChannels()
    {
        return $this->belongsToMany(WebChannel::class, 'tv_show_web_channel'); // Use 'show_web_channels' pivot table
    }

    public function castMembers()
    {
        return $this->belongsToMany(CastMember::class, 'tv_show_cast'); // Many-to-Many with CastMember model
    }

    public function crewMembers()
    {
        return $this->belongsToMany(CrewMember::class, 'tv_show_crew'); // Many-to-Many with CrewMember model
    }

    public function akas()
    {
        return $this->hasMany(Aka::class); // A show can have many AKAs
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'tv_show_genre'); // Belongs to many Genre models
    }

    public function seasons()
    {
        return $this->hasMany(Season::class);
    }

    public function pageViews()
    {
        return $this->morphMany(PageView::class, 'viewable');
    }
}
