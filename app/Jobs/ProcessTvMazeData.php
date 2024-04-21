<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\Client;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\TvShow;
use App\Models\Season;
use App\Models\Episode;
use App\Models\Genre;
use App\Models\Aka;
use App\Models\CastMember; // Assuming you have a CastMember model
use App\Models\CrewMember; // Assuming you have a CrewMember model
use App\Models\Network; // Assuming you have a Network model
use Illuminate\Support\Facades\Log; // For logging errors
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Str;

class ProcessTvMazeData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function handle(PendingRequest $client, string $baseUrl = 'https://api.tvmaze.com/shows')
    {
        while (true) {
            try {
                $response = $client->get($baseUrl . '/' . $this->id);
            } catch (\Illuminate\Http\Client\RequestException $exception) {
                // Handle 404 Not Found specifically
                if ($exception->getCode() === 404) {
                    Log::info("Show with ID {$this->id} not found. Skipping to next one.");
                    $this->id++;
                    continue;
                } else {
                    // Handle other client errors (e.g., network issues)
                    Log::error("Failed to get data for ID: {$this->id}. Error: " . $exception->getMessage());
                    // Implement retry mechanism or further error handling as needed
                    break; // Exit the loop for non-404 client errors
                }
            }

            try {
                if ($response->successful()) {
                    $data = $response->json();
                    $slug = Str::slug($data['name']);
                    $tvShow = TvShow::updateOrCreate(
                        ['maze_id' => $data['id']],
                        [
                            'url' => $data['url'],
                            'name' => $data['name'],
                            'type' => $data['type'],
                            'language' => $data['language'],
                            'status' => $data['status'],
                            'runtime' => $data['runtime'], // Direct access since it's not nested
                            'premiered' => $data['premiered'], // Direct access since it's not nested
                            'official_site' => $data['officialSite'], // Direct access since it's not nested
                            'rating' => isset($data['rating']['average']) ? $data['rating']['average'] : null,
                            'summary' => $data['summary'], // Direct access since it's not nested
                            'imdb_id' => isset($data['externals']['imdb']) ? $data['externals']['imdb'] : null,
                            'image_url' => isset($data['image']['medium']) ? $data['image']['medium'] : null,
                            'slug' => $slug,
                            // Other fields...
                    ]
                    );

                    // Process genres (if available)
                    if (isset($data['genres'])) {
                        $genres = [];
                        foreach ($data['genres'] as $genreData) {
                            $genre = Genre::firstOrCreate(['name' => $genreData]);
                            $genres[] = $genre->id;
                        }
                        $tvShow->genres()->sync($genres); // Attach genres to the TV show using the pivot table
                    }

                    // Process cast members (if available)
                    $castResponse = $client->get('https://api.tvmaze.com/shows/' . $this->id . '/cast');
                    if ($castResponse->successful()) {
                        $castData = $castResponse->json();
                        foreach ($castData as $castMemberData) {
                            $castMember = CastMember::firstOrCreate([
                                'name' => $castMemberData['person']['name'],
                                // ... other cast member data ...
                            ]);
                            $tvShow->castMembers()->attach($castMember->id);
                        }
                    } else {
                        Log::error('Failed to fetch cast data for show ' . $this->id . ': ' . $castResponse->status());
                    }

                    // Process crew members (if available)
                    $crewResponse = $client->get('https://api.tvmaze.com/shows/' . $this->id . '/crew');
                    if ($crewResponse->successful()) {
                        $crewData = $crewResponse->json();
                        foreach ($crewData as $crewMemberData) {
                            $crewMember = CrewMember::firstOrCreate([
                                'name' => $crewMemberData['person']['name'],
                                // ... other crew member data ...
                            ]);
                            $tvShow->crewMembers()->attach($crewMember->id);
                        }
                    } else {
                        Log::error('Failed to fetch crew data for show ' . $this->id . ': ' . $crewResponse->status());
                    }

                    // Process network (if available from main response)
                    if (isset($data['network'])) {
                        $network = Network::firstOrCreate([
                            'name' => $data['network']['name'],
                            // ... other network data ...
                        ]);
                        $tvShow->networks()->attach($network->id);
                    } else {
                        // Handle case where network information is not available in the main response
                        // You might need to use a separate endpoint based on the network ID
                        // ...
                    }

                    // Process AKAs (if available)
                    $akasResponse = $client->get('https://api.tvmaze.com/shows/' . $this->id . '/akas');
                    if ($akasResponse->successful()) {
                        $akasData = $akasResponse->json();
                        foreach ($akasData as $akaData) {
                            $tvShow->akas()->create([
                                'name' => $akaData['name'],
                                'country' => isset($akaData['country']['name']) ? $akaData['country']['name'] : null,
                                // ... other aka data ...
                            ]);
                        }
                    } else {
                        Log::error('Failed to fetch AKA data for show ' . $this->id . ': ' . $akasResponse->status());
                    }

                    $tvShow = TvShow::where('maze_id', $data['id'])->first();

                    // Check if the TV show exists
                    if ($tvShow) {
                        // Retrieve the corresponding tv_show_id
                        $tv_show_id = $tvShow->id;

                        // Process seasons and episodes using the correct tv_show_id
                        $episodeResponse = $client->get($baseUrl . '/' . $data['id'] . '/episodes');
                        if ($episodeResponse->successful()) {
                            $episodesData = $episodeResponse->json();
                            foreach ($episodesData as $episodeData) {
                                $season = Season::where('tv_show_id', $tv_show_id)
                                    ->where('season_number', $episodeData['season'])
                                    ->first();
                                if (!$season) {
                                    // If the season doesn't exist, create it
                                    $season = Season::create([
                                        'tv_show_id' => $tv_show_id,
                                        'season_number' => $episodeData['season']
                                    ]);
                                }

                                // Create or update the episode
                                $episode = Episode::updateOrCreate(
                                    ['maze_episode_id' => $episodeData['id']],
                                    [
                                        'season_id' => $season->id,
                                        'name' => $episodeData['name'],
                                        'airdate' => $episodeData['airdate'],
                                        'episode_number' => $episodeData['number'],
                                        'type' => $episodeData['type'],
                                        'summary' => $episodeData['summary'],
                                        'rating' => isset($data['rating']['average']) ? $data['rating']['average'] : null,
                                    ]
                                );
                            }
                        } else {
                            Log::error('Failed to fetch episode data for show ' . $data['id'] . ': ' . $episodeResponse->status());
                        }
                    } else {
                        Log::error('TV show with maze_id ' . $data['id'] . ' not found in the database.');
                    }
                } else {
                    Log::error('Failed to fetch data for show ' . $this->id . ': ' . $response->status());
                }
            } catch (\Exception $e) {
                Log::error('Error processing show data: ' . $e->getMessage());
            }

            // Increment $id for the next iteration
            $this->id++;
        }
    }
}
