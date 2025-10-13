<?php

use App\Http\Controllers\{ActorController, MovieController, LinkController, GenreController};

Route::apiResource('actors', ActorController::class);
Route::apiResource('movies', MovieController::class);
Route::apiResource('links', LinkController::class);
Route::apiResource('genres', GenreController::class);