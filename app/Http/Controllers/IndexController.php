<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Episode;

class IndexController extends Controller
{
    public function index()
    {
        $startDate = Carbon::yesterday();
        $endDate = Carbon::now()->addDays(4);
        
        $episodes = Episode::whereBetween('airdate', [$startDate, $endDate])->get();
        
        return view('welcome', ['episodes' => $episodes]);
    }
}
