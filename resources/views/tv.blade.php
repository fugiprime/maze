@extends('layouts.app')
@section('content')
<div class="container px-2">
    <div class="row g-2">
        <div class="col-md-10 main-container"> 
            <div class="row g-2">
                @foreach ($tvShows as $tvShow)
                    @php
                        // Extract year from premiered date
                        $year = date('Y', strtotime($tvShow->premiered));
                        // Limit summary to 200 characters
                        $summary = strlen($tvShow->summary) > 200 ? substr($tvShow->summary, 0, 200) . '...' : $tvShow->summary;
                    @endphp
                    <div class="col-md-2">
                        <a href="{{ route('watch', ['id' => $tvShow->id, 'slug' => $tvShow->slug]) }}" class="card-link" data-bs-toggle="popover" data-bs-html="true" data-bs-placement="auto" title="{{ $tvShow->name }} ({{ $year }})" data-bs-content="
                            <div class='d-flex flex-column'>
                                <div class='fw-bold mb-2'>Rating: {{ $tvShow->rating }}</div>
                                <div class='fw-bold mb-2'>Summary:</div>
                                <div>{{ $summary }}</div>
                            </div>
                        "> <!-- Link to the show blade -->
                            <div class="card g-2 position-relative">
                                <img class="card-img-top" src="{{ $tvShow->image_url }}" alt="{{ $tvShow->name }}">
                                <div class="card-img-overlay"></div> <!-- Overlay for gradient -->
                                <div class="status-badge position-absolute bottom-0 start-0 end-0 bg-dark p-2 text-center" style="opacity: 0.7;">
                                    <p class="card-text text-white mb-0">{{ $tvShow->status }}</p>
                                </div>
                                <div class="rating-badge position-absolute top-0 end-0 bg-dark p-2" style="opacity: 0.7;">
                                    <p class="card-text text-white mb-0">{{ $tvShow->rating }}</p>
                                </div>
                            </div>
                            <div class="card mb-2 bg-transparent border-0">
                                <div class="card-body p-2 text-center">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <p class="card-subtitle mb-0" style="color: #EBEBEB; font-size: 12px;">({{ $year }})</p>
                                        <p class="card-text mb-0" style="color: #EBEBEB; font-size: 12px;">{{ $tvShow->type }}</p>
                                    </div>
                                    <p class="card-title text-white">{{ $tvShow->name }}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    @if ($loop->iteration % 6 == 0)
                </div>
                <div class="row g-2">
                    @endif
                @endforeach
            </div>
            {{ $tvShows->links('pagination::bootstrap-4') }}
        </div>
        <div class="col-md-2 main-container">
            <h5>Popular Shows</h5>
            @foreach ($popularTvShows as $popularTvShow)
                <div class="mb-3">
                    <a href="{{ route('watch', ['id' => $popularTvShow->id, 'slug' => $popularTvShow->slug]) }}" class="poplink">
                        <img src="{{ $popularTvShow->image_url }}" alt="{{ $popularTvShow->name }}" class="img-fluid">
                        <p class="text-center">{{ $popularTvShow->name }}</p>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>
    .card-link {
        text-decoration: none; /* Remove text decoration */
    }

    .poplink {
        text-decoration: none;
        color: #EBEBEB;
    }

    .card:hover .card-img-overlay {
        background: linear-gradient(to top, rgba(0, 193, 255, 0.6), rgba(0, 193, 255, 0)); /* Gradient from bottom to transparent top */
    }

    .main-container {
        background-color: #1D3138; /* Background color */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Box shadow */
        padding: 20px; /* Add padding for better appearance */
        border-radius: 10px; /* Optional: Add border radius for rounded corners */
    }

    /* Custom popover styling */
    .popover {
        background-color: #0F5E66; /* Popover background color */
    }

    .popover-header {
        color: #F27460; /* Header color */
        font-weight: bold;
    }

    .popover-body {
        color: white; /* Body text color */
    }

    .popover .fw-bold {
        font-weight: bold;
    }
</style>

<script>
    $(function () {
        // Initialize Bootstrap popovers
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    });
</script>

@endsection
