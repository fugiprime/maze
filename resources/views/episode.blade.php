@extends('layouts.app')

@section('title', $tvShow->name . ($tvShow->premiered ? ' (' . date('Y', strtotime($tvShow->premiered)) . ')' : '') . ' Season ' . $episode->season->season_number . ' Episode ' . $episode->episode_number . ' - ' . $episode->name)
@if ($episode->summary)
    @section('description', strlen(strip_tags($episode->summary)) > 155 ? substr(strip_tags($episode->summary), 0, 155) . '...' : strip_tags($episode->summary))
@else
    @section('description', "Watch all the latest TV shows online without downloading them")
@endif
@section('content')

    <!-- Iframe container -->
    <div class="container main-container mb-2">
        <iframe id="embedFrame" src="https://vidsrc.xyz/embed/tv?imdb={{ $tvShow->imdb_id }}&season={{ $episode->season_number }}&episode={{ $episode->episode_number }}" width="100%" height="500" frameborder="0" allowfullscreen></iframe>

        <div class="col-md-12 text-center">
            <button id="toggleVidsrc" class="btn btn-secondary">VidsrcPro</button>
            <button id="toggleVidsrcme" class="btn btn-secondary">Vidsrc</button>
            <button id="togglePrimewire" class="btn btn-primary mr-2">Primewire</button>
        </div>
    </div>
    <div class="row mt-4 mb-3">
        <div class="col-md-12 text-center">
        @if ($prevEpisode)
            <a href="{{ route('episode', ['id' => $tvShow->id, 'season_number' => $prevEpisode->season->season_number, 'episode_number' => $prevEpisode->episode_number]) }}" class="btn btn-info"><< Previous Episode </a>
        @endif
        <a href="{{ route('watch', ['id' => $tvShow->id, 'slug' => $tvShow->slug]) }}" class="btn btn-success" style="text-decoration: none; color:white;"> All Episodes </a>
        @if ($nextEpisode)
            <a href="{{ route('episode', ['id' => $tvShow->id, 'season_number' => $nextEpisode->season->season_number, 'episode_number' => $nextEpisode->episode_number]) }}" class="btn btn-info">Next Episode >></a>
        @endif
    </div>
    </div>
     
    <div class="container main-container mb-2">
        <div class="row">
            <!-- Poster on the left -->
            <div class="col-md-2">
                <img src="{{ $tvShow->image_url }}" alt="{{ $tvShow->name }}" class="img-fluid">
            </div>
            <!-- Episode information in the center -->
            <div class="col-md-6">
                <p>Summary: {!! $episode->summary !!}</p>
                <h5 class="mb-0">Rating: <span class="{{ $episode->rating >= 0 && $episode->rating < 5 ? 'text-danger' : ($episode->rating >= 5 && $episode->rating < 6 ? 'text-success' : ($episode->rating >= 6 && $episode->rating < 7 ? 'text-primary' : 'text-info')) }}">{{ $episode->rating }}</span></h5>
            </div>
            <!-- Main TV show information on the right -->
            <div class="col-md-4">
                <h2><a href="{{ route('watch', ['id' => $tvShow->id, 'slug' => $tvShow->slug]) }}" style="text-decoration: none; color:white;">
                   {{ $tvShow->name }}</a></h2>
                <p class="mb-0">Status: {{ $tvShow->status }}</p>
                <p class="mb-0">First Aired: {{ $tvShow->premiered }}</p>
                    <p> -------------------------------- </p>
                <h4 class="mb-0">Episode Name : {{ $episode->name }}</h4>
                <p class="mb-0">Airdate: {{ $episode->airdate }}</p>
                <p class="mb-0">Episode Number: {{ $episode->episode_number }}</p>
                <p class="mb-0">Type: {{ $episode->type }}</p>
                
            </div>
        </div>
    </div>

    

<style>
    .hidden {
        display: none;
    }

    /* Custom CSS for main container */
    .main-container {
        background-color: #1D3138; /* Background color */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Box shadow */
        padding: 20px; /* Add padding for better appearance */
        border-radius: 10px; /* Optional: Add border radius for rounded corners */
    }

    .text-danger {
        color: red;
    }

    .text-success {
        color: #9DFF59;
    }

    .text-primary {
        color: #09B81C;
    }

    .text-info {
        color: #067E8A;
    }

</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const primewireButton = document.getElementById('togglePrimewire');
        const vidsrcButton = document.getElementById('toggleVidsrc');
        const vidsrcmeButton = document.getElementById('toggleVidsrcme');
        const embedFrame = document.getElementById('embedFrame');

        
        vidsrcButton.addEventListener('click', function() {
            embedFrame.src = 'https://vidsrc.xyz/embed/tv?imdb={{ $tvShow->imdb_id }}&season={{ $episode->season_number }}&episode={{ $episode->episode_number }}';
        });
        
        vidsrcmeButton.addEventListener('click', function() {
            embedFrame.src = 'https://vidsrc.to/embed/tv/{{ $tvShow->imdb_id }}/{{ $episode->season_number }}/{{ $episode->episode_number }}';
        });

        primewireButton.addEventListener('click', function() {
            embedFrame.src = 'https://www.primewire.tf/embed/tv?tvmaze={{ $tvShow->maze_id }}&season={{ $episode->season_number }}&episode={{ $episode->episode_number }}';
        });
    });
</script>
@endsection
