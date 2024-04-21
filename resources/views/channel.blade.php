@extends('layouts.app')
@section('content')
<div class="container main-container">
    <h1>TV Shows Filtered by Web Channel: {{ $webChannelName }}</h1>
    <div class="row">
        @foreach($tvShows as $tvShow)
        <div class="col-md-2 mb-4">
            <div class="card">
                <img src="{{ $tvShow->image_url }}" class="card-img-top" alt="{{ $tvShow->name }}">
            </div>
            <div>
                <h5 class="card-title text-center">{{ $tvShow->name }}</h5>
                <p class="card-text text-center">({{ $tvShow->premiered ? \Carbon\Carbon::parse($tvShow->premiered)->year : 'N/A' }})</p>
                <!-- Add more details if needed -->
            </div>
        </div>
        @if ($loop->iteration % 6 == 0)
    </div>
    <div class="row">
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
</style>
@endsection
