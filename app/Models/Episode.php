<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
    protected $fillable = ['maze_episode_id', 'name', 'airdate', 'episode_number', 'type', 'summary', 'season_id', 'rating'];

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function pageViews()
    {
        return $this->morphMany(PageView::class, 'viewable');
    }

}
