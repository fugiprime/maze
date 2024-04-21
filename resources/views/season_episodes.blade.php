@foreach ($episodes as $key => $episode)
    @php
        $backgroundColor = $key % 2 == 0 ? '#054256' : '#202C30';
    @endphp
    <div class="episode-container" style="background-color: {{ $backgroundColor }};">
        <a href="{{ route('episode', ['id' => $tvShow->id, 'season_number' => $season_number, 'episode_number' => $episode->episode_number]) }}" class="episode-link" style="text-decoration: none; color: white;">
           <p class="mb-0" style="font-weight: bold; font-family: Arial, sans-serif;"> Episode {{ $episode->episode_number }}: {{ $episode->name }} </p>
        </a>
    </div>
@endforeach
