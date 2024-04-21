@extends('layouts.app')

@section('title', ($tvShow->name ?? 'TV Show Details') . ($tvShow->premiered ? ' (' . date('Y', strtotime($tvShow->premiered)) . ')' : ''))
@section('description', isset($tvShow->summary) && strlen(strip_tags($tvShow->summary)) > 155 ? substr(strip_tags($tvShow->summary), 0, 155) . '...' : (isset($tvShow->summary) ? strip_tags($tvShow->summary) : 'No description available'))

@section('content')
<div class="container main-container mb-2">
    <div class="row">
        <!-- Poster on the left -->
        <div class="col-md-2">
            <img src="{{ $tvShow->image_url }}" alt="{{ $tvShow->name }}" class="img-fluid">
            <p class="mb-0 text-center"> Status: {{ $tvShow->status }} </p>
            <p class="mb-0 text-center" style="color: #1ACFCF "> First Aired : {{ $tvShow->premiered }}</p>
            <div class="text-center">
            @php
            $averageRating = $tvShow->rating; // Assuming $tvShow->rating contains the average rating out of 10
            $percentage = ($averageRating / 10) * 100;
            $fullStars = floor($percentage / 20); // Determine number of full stars
            $partialStarWidth = ($percentage % 20) . '%'; // Determine width of partial star
        @endphp

        <div class="rating">
            <!-- Full stars -->
            @for ($i = 0; $i < $fullStars; $i++)
                <i class="fas fa-star"></i>
            @endfor

            <!-- Partial star -->
            @if ($percentage % 20 != 0)
                <i class="fas fa-star-half-alt" style="width: {{ $partialStarWidth }}"></i>
            @endif

            <!-- Empty stars -->
            @for ($i = $fullStars + 1; $i < 5; $i++)
                <i class="far fa-star"></i>
            @endfor
        </div>
        </div>
        <p class="mb-0 text-center" style="color: #009999">Views: {{ $pageViewsCount }}</p>
        </div>
        <!-- Summary, networks, webchannels, genres, and akas at the center -->
        <div class="col-md-6">
            <h2>{{ $tvShow->name }} ({{ date('Y', strtotime($tvShow->premiered)) }})</h2>
            <div>{!! $tvShow->summary !!}</div>
                <strong>Networks:</strong> 
                    @if ($tvShow->networks)
                        @foreach ($tvShow->networks as $network)
                        <a href="{{ route('network', ['network' => $network->id])}}" style="color: #21A5FB;">{{ $network->name }} </a>
                            @if (!$loop->last)
                                ,
                            @endif
                        @endforeach
                    @else
                        N/A
                    @endif
                    <br>
                <strong>Web Channels:</strong> 
                    @if ($tvShow->webChannels)
                        @foreach ($tvShow->webChannels as $webChannel)
                        <a href="{{ route('channel', ['channel' => $webChannel->id]) }}" style="color: #06B35D;"> {{ $webChannel->name }} </a>
                            @if (!$loop->last)
                                ,
                            @endif
                        @endforeach
                    @else
                        N/A
                    @endif
                    <br>
                <strong>Genres:</strong> 
                    @if ($tvShow->genres)
                        @foreach ($tvShow->genres as $genre)
                        <a href="{{ route('genre', ['genre' => $genre->name]) }}" style="color: #06B35D;">{{ $genre->name }} </a>
                            @if (!$loop->last)
                                ,
                            @endif
                        @endforeach
                    @else
                        N/A
                    @endif
                    <br>
                <strong>AKAs:</strong> 
                    @if ($tvShow->akas)
                        @foreach ($tvShow->akas as $aka)
                            {{ $aka->name }}
                            @if (!$loop->last)
                                ,
                            @endif
                        @endforeach
                    @else
                        N/A
                    @endif                
        </div>
        <!-- Cast and crew on the right side -->
        <div class="col-md-4">
            <h3> Tv Show Cast & Crew</h3>
            <p><strong>Cast:</strong> 
                @if ($tvShow->castMembers->isNotEmpty())
                    @foreach ($tvShow->castMembers->take(8) as $castMember)
                        <span>{{ $castMember->name }}</span>{{ !$loop->last ? ',' : '' }}
                    @endforeach
                    @if ($tvShow->castMembers->count() > 8)
                        <span class="hidden" id="castList">
                            @foreach ($tvShow->castMembers->slice(8) as $castMember)
                                <span>{{ $castMember->name }}</span>{{ !$loop->last ? ',' : '' }}
                            @endforeach
                        </span>
                        <button id="castButton" onclick="toggleCast()">Show More</button>
                    @endif
                @else
                    N/A
                @endif
            </p>
            <p><strong>Crew:</strong> 
                @if ($tvShow->crewMembers->isNotEmpty())
                    @foreach ($tvShow->crewMembers->take(8) as $crewMember)
                        <span>{{ $crewMember->name }}</span>{{ !$loop->last ? ',' : '' }}
                    @endforeach
                    @if ($tvShow->crewMembers->count() > 8)
                        <span class="hidden" id="crewList">
                            @foreach ($tvShow->crewMembers->slice(8) as $crewMember)
                                <span>{{ $crewMember->name }}</span>{{ !$loop->last ? ',' : '' }}
                            @endforeach
                        </span>
                        <button id="crewButton" onclick="toggleCrew()">Show More</button>
                    @endif
                @else
                    N/A
                @endif
            </p>
        </div>
    </div>
</div>
<div class="container main-container mb-2">
    <!-- Seasons dropdown -->
    <div class="row">
        <div class="col-md-12">
            <h3>Seasons and Episodes</h3> 
            @if ($lastAiredEpisode)
            <div>
                <p>Last Aired Episode:
                    @if ($lastAiredEpisode->season && $lastAiredEpisode->season->tvShow)
                        <a href="{{ route('episode', ['id' => $lastAiredEpisode->season->tvShow->id, 'season_number' => $lastAiredEpisode->season->season_number, 'episode_number' => $lastAiredEpisode->episode_number]) }}" style="text-decoration: none; color:#FF3A00;">
                    @endif
                        {{ $lastAiredEpisode->season->tvShow->name ?? 'Unknown' }} -
                        Season - {{ $lastAiredEpisode->season->season_number ?? 'Unknown' }} -
                        Episode - {{ $lastAiredEpisode->episode_number ?? 'Unknown' }} -
                        {{ $lastAiredEpisode->name ?? 'Unknown' }}
                    @if ($lastAiredEpisode->season && $lastAiredEpisode->season->tvShow)
                        </a>
                    @endif
                </p>
            </div>
        @endif
            <!-- Episodes list -->
            <div>
                <select id="seasonDropdown" class="form-select mb-3">
                    <option value="" selected disabled>Select Season</option>
                    @foreach ($tvShow->seasons as $season)
                        <option value="{{ $season->season_number }}">Season {{ $season->season_number }}</option>
                    @endforeach
                </select>

                <div id="episodesContainer">
                    <!-- Episodes for the selected season will be displayed here -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Related Shows Section -->
<div class="container main-container mb-2">
    <div class="row">
        <div class="col-md-12">
            <h3>Related Shows</h3>
            <div class="row">
                @foreach ($relatedShows as $relatedShow)
                    <div class="col-md-2"> <a href="{{ route('watch', ['id' => $relatedShow->id, 'slug' => $relatedShow->slug]) }}" style="text-decoration: none; color:white;">
                        <div class="card">
                            <img src="{{ $relatedShow->image_url }}" class="card-img-top" alt="{{ $relatedShow->name }}">
                        </div>

                        <div class="mt-2">
                            <h5 class="text-center">{{ $relatedShow->name }}</h5>
                            <p class="text-center">({{ date('Y', strtotime($relatedShow->premiered)) }}) </p>
                        </div> </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
<!-- End Related Shows Section -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<script>
    function toggleCast() {
        var castList = document.getElementById('castList');
        castList.classList.toggle('hidden');
        var button = document.querySelector('#castButton');
        button.textContent = button.textContent === 'Show More' ? 'Show Less' : 'Show More';
    }

    function toggleCrew() {
        var crewList = document.getElementById('crewList');
        crewList.classList.toggle('hidden');
        var button = document.querySelector('#crewButton');
        button.textContent = button.textContent === 'Show More' ? 'Show Less' : 'Show More';
    }

    document.getElementById('seasonDropdown').addEventListener('change', function() {
    var selectedSeasonNumber = this.value;

    // Fetch episodes for the selected season using AJAX
    fetch(`/tv/{{ $tvShow->id }}/seasons/${selectedSeasonNumber}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('episodesContainer').innerHTML = data;
        });
});
</script>

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

    .rating {
        font-size: 20px; /* Adjust size as needed */
    }
</style>

@endsection
