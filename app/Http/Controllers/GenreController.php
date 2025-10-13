<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Genre;

class GenreController extends Controller
{
    public function index(Request $request)
    {
        $genres = Genre::orderBy('title')
                        ->get();
        return response()->json($genres);
    }
}
