<?php

namespace App\Console\Commands;

use App\Jobs\ProcessTvMazeData; // Replace with your actual Job class name
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class GetTvMazeData extends Command
{
    
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-tv-maze-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->dispatch(new ProcessTvMazeData(1)); // Dispatch job with starting ID

        $this->info('Successfully initiated TVMaze data processing!');
    }
}
