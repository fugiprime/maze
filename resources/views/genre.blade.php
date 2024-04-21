@extends('layouts.app')

@section('title', 'TV Shows Filtered by Genre')

@section('content')
    <div class="container main-container">
        <h1>TV Shows Filtered by Genre: {{ $genre }}</h1>
        <div class="row">
            @foreach($tvShows as $tvShow)
                <div class="col-md-2 mb-4">
                    <div class="card">
                        <a href="{{ route('watch', ['id' => $tvShow->id, 'slug' => $tvShow->slug]) }}" class="poster-link">
                            <img src="{{ $tvShow->image_url }}" class="card-img-top" alt="{{ $tvShow->name }}">
                            <div class="image-overlay"></div> <!-- Gradient overlay -->
                        </a>
                    </div>
                    <div>
                        <h5 class="card-title text-center title-limit">{{ $tvShow->name }}</h5>
                        <p class="card-text text-center">({{ $tvShow->premiered ? \Carbon\Carbon::parse($tvShow->premiered)->year : 'N/A' }})</p>
                        <!-- Add more details if needed -->
                    </div>
                </div>
                @if ($loop->iteration % 6 == 0)
                    </div><div class="row">
                @endif
            @endforeach
        </div>
        <div class="d-flex justify-content-center mt-4">
            {{ $tvShows->links('pagination::bootstrap-4') }} <!-- Pagination links -->
        </div>
    </div>

<style>
    .main-container {
        background-color: #1D3138; /* Background color */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Box shadow */
        padding: 20px; /* Add padding for better appearance */
        border-radius: 10px; /* Optional: Add border radius for rounded corners */
    }
    
    .poster-link {
        display: block;
        width: 100%;
        position: relative; /* Needed for overlay */
    }
    
    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to top, #057483, transparent); /* Gradient from #057483 at bottom to transparent at top */
        opacity: 0; /* Initially hidden */
        transition: opacity 0.3s ease; /* Smooth transition */
    }
    
    .card:hover .image-overlay {
        opacity: 1; /* Show gradient overlay on hover */
    }

    .title-limit {
        white-space: nowrap; /* Prevent wrapping */
        overflow: hidden; /* Hide overflow */
        text-overflow: ellipsis; /* Display ellipsis (...) for overflow text */
    }
</style>
@endsection
