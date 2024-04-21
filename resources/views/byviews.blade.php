@extends('layouts.app')

@section('title', 'TV Shows by Views')

@section('content')
    <div class="container main-container">
        <h1>TV Shows by Views</h1>
        <div class="row">
            @foreach($tvShows as $tvShow)
                <div class="col-md-2 mb-4">
                    <div class="card position-relative">
                        <!-- Poster with rating on top -->
                        <div class="position-relative">
                            <a href="{{ route('watch', ['id' => $tvShow->id, 'slug' => $tvShow->slug]) }}" class="poster-link">
                                <img src="{{ $tvShow->image_url }}" class="card-img-top" alt="{{ $tvShow->name }}">
                            </a>
                            <div class="rating-badge">{{ $tvShow->rating }}</div>
                        </div>
                        <!-- Status badge running from end to end -->
                        <div class="status-badge">
                            <span class="status-text">{{ $tvShow->status }}</span>
                        </div>
                    </div>
                    <div class="mt-1">
                        <h6 class="card-title text-center" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $tvShow->name }}</h6>
                        <p class="card-text text-center mb-0">({{ $tvShow->premiered ? \Carbon\Carbon::parse($tvShow->premiered)->year : 'N/A' }})</p>
                        <p class="card-text text-center">Views: {{ $tvShow->views }}</p>
                    </div>
                </div>
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

        .rating-badge {
            position: absolute;
            top: 0;
            right: 0;
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 5px 10px;
            border-top-left-radius: 5px;
        }

        .status-badge {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: rgba(0, 0, 0, 0.8);
            text-align: center;
            padding: 5px 0;
        }

        .status-text {
            color: white;
            border-radius: 5px;
        }

        .poster-link {
            display: block;
            width: 100%;
            height: 100%;
        }

        .poster-link:hover .card-img-top {
            opacity: 0.7;
        }
    </style>
@endsection
