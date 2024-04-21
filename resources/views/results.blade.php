@extends('layouts.app')

@section('content')
<div class="container main-container mt-3">
    <h2>Search Results for "{{ $query }}"</h2>
    <div class="row">
        @foreach($results as $tvShow)
            <div class="col-md-2 mb-4">
                <div class="card position-relative">
                    <a href="{{ route('watch', ['id' => $tvShow->id, 'slug' => $tvShow->slug]) }}" style="text-decoration: none">
                    <img src="{{ $tvShow->image_url }}" class="card-img-top" alt="{{ $tvShow->name }}">
                    <div class="overlay"></div> <!-- Gradient overlay -->
                    <div class="rating-badge">{{ $tvShow->rating }}</div> <!-- Rating badge -->
                </div>
                <div class="card-body">
                    <h6 class="card-title text-center mt-1" style="color: #03D1D1">{{ $tvShow->name }}</h6>
                    <p class="card-text text-center" style="color: #03BABA">({{ $tvShow->premiered ? \Carbon\Carbon::parse($tvShow->premiered)->year : 'N/A' }})</p>
                </a>
                </div>
            </div>
            @if ($loop->iteration % 6 == 0)
                <div class="w-100"></div> <!-- Clear the row after every 6 items -->
            @endif
        @endforeach
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
        top: 10px;
        right: 10px;
        background-color: #046165;
        color: #03BABA;
        padding: 5px 10px;
        border-radius: 5px;
    }

    .card {
        position: relative;
        overflow: hidden;
    }

    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to bottom, rgba(0,0,0,0.5) 0%, rgba(0,0,0,0) 100%);
        opacity: 0; /* Initially hidden */
        transition: opacity 0.3s ease; /* Transition effect */
    }

    .card:hover .overlay {
        opacity: 1; /* Show on hover */
    }
</style>
@endsection
