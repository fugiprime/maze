<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\IndexController::class, 'index']);

Route::get('/tv', [App\Http\Controllers\TvShowController::class, 'index'])->name('tvshows');
Route::get('/tv/{id}-{slug}', [App\Http\Controllers\TvShowController::class, 'show'])->name('watch');
Route::get('/tv/{id}/seasons/{season_number}', [App\Http\Controllers\TvShowController::class, 'getSeasonEpisodes']);
Route::get('/tv/{id}/season-{season_number}-episode-{episode_number}', [App\Http\Controllers\TvShowController::class, 'showEpisode'])->name('episode');
Route::get('/tv/{id}/{season_number}-{episode_number}/previous', [App\Http\Controllers\TvShowController::class, 'previousEpisode'])->name('episode.previous');
Route::get('/tv/{id}/{season_number}-{episode_number}/next', [App\Http\Controllers\TvShowController::class, 'nextEpisode'])->name('episode.next');
Route::get('/tv/byviews', [App\Http\Controllers\TvShowController::class, 'sortByViews'])->name('byviews');
Route::get('/tv/bygenre/{genre}', 'App\Http\Controllers\TvShowController@filterByGenre')->name('genre');
Route::get('/network/{network}', 'App\Http\Controllers\TVShowController@showByNetwork')->name('network');
Route::get('webchannel/{channel}', 'App\Http\Controllers\TVShowController@showByWebChannel')->name('channel');
Route::get('/tv/cast/{castMember}', 'App\Http\Controllers\TVShowController@showByActor')->name('cast');
Route::get('/tv/crew/{crewMember}', 'App\Http\Controllers\TVShowController@showByCrew')->name('crew');
Route::get('/search', 'App\Http\Controllers\SearchController@search')->name('search');


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
