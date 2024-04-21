<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TvShow;
use App\Models\Episode;
use App\Models\Network;
use App\Models\WebChannel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TvShowController extends Controller
{
    public function index()
    {
        $tvShows = TvShow::paginate(60);
        
        $popularTvShows = TvShow::select('tv_shows.id', 'tv_shows.name', 'tv_shows.slug', 'tv_shows.image_url', DB::raw('COUNT(*) AS page_views_count'))
            ->join('page_views', 'page_views.viewable_id', '=', 'tv_shows.id')
            ->where('viewable_type', 'App\Models\TvShow')
            ->groupBy('tv_shows.id', 'tv_shows.name', 'tv_shows.slug', 'tv_shows.image_url')
            ->orderByDesc('page_views_count')
            ->take(10)
            ->get();


    // Retrieve all TV shows (if needed)

        return view('tv', ['tvShows' => $tvShows, 'popularTvShows' => $popularTvShows]);
    }

    public function show($id, $slug)
    {
        $tvShow = TvShow::where('id', $id)->where('slug', $slug)->firstOrFail();
        $tvShow->pageViews()->create();
        $pageViewsCount = DB::table('page_views')
        ->where('viewable_type', 'App\Models\TvShow')
        ->where('viewable_id', $id)
        ->count();
        $relatedShows = TvShow::whereHas('genres', function ($query) use ($tvShow) {
            $query->whereIn('genres.id', $tvShow->genres->pluck('id'));
        })->where('id', '!=', $tvShow->id)->take(18)->get();

        $lastAiredEpisode = Episode::whereHas('season', function ($query) use ($id) {
            $query->where('tv_show_id', $id);
        })->whereDate('airdate', '<=', Carbon::tomorrow())->orderBy('airdate', 'desc')->first();

        return view('watch', [
            'tvShow' => $tvShow,
            'pageViewsCount' => $pageViewsCount,
            'relatedShows' => $relatedShows,
            'lastAiredEpisode' => $lastAiredEpisode,
        ]);
    }

    public function getSeasonEpisodes(Request $request, $id, $season_number)
    {
        $tvShow = TvShow::findOrFail($id);
        $season = $tvShow->seasons()->where('season_number', $season_number)->firstOrFail();
        $episodes = $season->episodes;

        return view('season_episodes', ['tvShow' => $tvShow, 'episodes' => $episodes, 'season_number' => $season_number]);
    }


    public function showEpisode($id, $season_number, $episode_number)
    {
        try {
            // Find the episode by TV show ID, season number, and episode number
            $episode = Episode::whereHas('season', function ($query) use ($id, $season_number) {
                $query->where('tv_show_id', $id)->where('season_number', $season_number);
            })->where('episode_number', $episode_number)->firstOrFail();

            // Retrieve the corresponding TV show
            $tvShow = $episode->season->tvShow;
            $episode->pageViews()->create();
            // Find the previous episode
            $prevEpisode = Episode::where('season_id', $episode->season_id)
                ->where('episode_number', '<', $episode_number)
                ->orderBy('episode_number', 'desc')
                ->first();

            // Find the next episode
            $nextEpisode = Episode::where('season_id', $episode->season_id)
                ->where('episode_number', '>', $episode_number)
                ->orderBy('episode_number')
                ->first();

            // Pass the episode, previous episode, next episode, and TV show to the view
            return view('episode', compact('episode', 'prevEpisode', 'nextEpisode', 'tvShow'));
        } catch (\Exception $e) {
            // Log the error or handle it in an appropriate way
            return redirect()->route('tvshows.index')->with('error', 'Episode not found. Search the show again to regenerate');
        }
    }

    public function previousEpisode($id, $season_number, $episode_number)
    {
        // Find the previous episode
        $prevEpisode = Episode::where('tv_show_id', $id)
            ->where('season_number', $season_number)
            ->where('episode_number', '<', $episode_number)
            ->orderBy('episode_number', 'desc')
            ->first();

        // Redirect to the previous episode if found
        if ($prevEpisode) {
            return redirect()->route('episode', [
                'id' => $id,
                'season_number' => $season_number,
                'episode_number' => $prevEpisode->episode_number
            ]);
        } else {
            // Handle case where there is no previous episode
            // For example, redirect to the current episode page
            return redirect()->route('episode', [
                'id' => $id,
                'season_number' => $season_number,
                'episode_number' => $episode_number
            ]);
        }
    }

    public function nextEpisode($id, $season_number, $episode_number)
    {
        // Find the next episode
        $nextEpisode = Episode::where('tv_show_id', $id)
            ->where('season_number', $season_number)
            ->where('episode_number', '>', $episode_number)
            ->orderBy('episode_number')
            ->first();

        // Redirect to the next episode if found
        if ($nextEpisode) {
            return redirect()->route('episode', [
                'id' => $id,
                'season_number' => $season_number,
                'episode_number' => $nextEpisode->episode_number
            ]);
        } else {
            // Handle case where there is no next episode
            // For example, redirect to the current episode page
            return redirect()->route('episode', [
                'id' => $id,
                'season_number' => $season_number,
                'episode_number' => $episode_number
            ]);
        }
    }

    public function sortByViews()
    {
        $tvShows = TvShow::join('page_views', 'tv_shows.id', '=', 'page_views.viewable_id')
        ->where('page_views.viewable_type', 'App\Models\TvShow')
        ->select('tv_shows.id', 'tv_shows.name', 'tv_shows.image_url', 'premiered', 'rating', 'status', 'slug', DB::raw('COUNT(page_views.id) as views'))
        ->groupBy('tv_shows.id', 'tv_shows.name', 'tv_shows.image_url', 'premiered', 'rating', 'status' , 'slug') // Include all non-aggregated columns in the GROUP BY clause
        ->orderByDesc('views')
        ->paginate(30);

        return view('byviews', compact('tvShows'));
    }


    public function filterByGenre($genre)
    {
        // Retrieve TV shows that match the given genre
        $tvShows = TvShow::whereHas('genres', function ($query) use ($genre) {
            $query->where('name', $genre);
        })->paginate(30);

        // Pass the filtered TV shows to the view
        return view('genre', compact('tvShows', 'genre'));
    }

    public function showByNetwork(Network $network)
    {
        // Retrieve TV shows associated with the given network and paginate the results
        $tvShows = $network->tvShows()->paginate(30);
        $networkName = $network->name;
        // Pass the retrieved TV shows and the network name to the view
        return view('network', compact('tvShows', 'network', 'networkName'));
    }


    public function showByWebChannel(WebChannel $webChannel)
    {
        $tvShows = $webChannel->tvShows()->paginate(30);
        $webChannelName = $webChannel->name;
        return view('channel', compact('tvShows', 'channel', 'webChannelName'));
    }


}
