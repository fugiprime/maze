<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TvShow;
use App\Models\Aka;


class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');

        // Search in TV shows table for title and IMDb ID
        $results = TvShow::where('name', 'like', "%$query%")
                        ->orWhere('imdb_id', $query)
                        ->get();

        // Search in AKAs table for names
        $akas = Aka::where('name', 'like', "%$query%")->get();

        // Merge results from TV shows and AKAs
        foreach ($akas as $aka) {
            // Avoid duplicate results
            if (!$results->contains('id', $aka->tv_show_id)) {
                $results->push($aka->tvShow);
            }
        }

        return view('results', compact('results', 'query'));
    }

}
