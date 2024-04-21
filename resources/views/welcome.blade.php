@extends('layouts.app')

@section('content')


    <div class="container main-container">
        @foreach ($episodes->groupBy('airdate') as $date => $episodesOnDate)
            <div class="row mb-4">
                <div class="col ">
                    <h3>{{ $date }}</h3>
                </div>
            </div>
            <div class="row">
                @foreach ($episodesOnDate as $episode)
                    <div class="col-md-2">
                        <div class="card position-relative"><a href="{{ route('episode', ['id' => $episode->season->tvShow->id, 'season_number' => $episode->season->season_number, 'episode_number' => $episode->episode_number]) }}" style="text-decoration: none; color: white;">
                            <img src="{{ $episode->season->tvShow->image_url }}" class="card-img-top" alt="{{ $episode->season->tvShow->name }}">
                            <div class="overlay"></div> <!-- Gradient overlay -->
                        </div>
                        <div>
                            <p class="mb-0 text-center"> {{ $episode->season->tvShow->name }}</p>
                        <p class="text-center">S{{ str_pad($episode->season->season_number, 2, '0', STR_PAD_LEFT) }}
                            E{{ str_pad($episode->episode_number, 2, '0', STR_PAD_LEFT) }}</p></a>
                        </div>
                    </div> 
                @endforeach
            </div>
        @endforeach
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
        background: linear-gradient(to top, rgba(0, 193, 255, 0.6), rgba(0, 193, 255, 0));
        opacity: 0; /* Initially hidden */
        transition: opacity 0.3s ease; /* Transition effect */
    }

    .card:hover .overlay {
        opacity: 1; /* Show on hover */
    }
    </style>
@endsection
