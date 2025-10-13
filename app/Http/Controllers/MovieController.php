<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Movie;
use App\Traits\FetchesImage;
use App\Rules\ImageOrBase64;

class MovieController extends Controller
{
    use FetchesImage;

    public function index(Request $request)
    {
        $q = $request->query('q');
        $movies = Movie::when($q, fn($query) => $query->where('title', 'like', "%$q%"))
                        ->with('genres')
                        ->orderBy('title')
                        ->get();
        return response()->json($movies);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'link' => 'nullable|url',
            'image' => ['nullable', new ImageOrBase64],
        ]);

        $imagenBase64 = $this->fetchImageBase64($request->image);

        $movie = Movie::create([
            'title' => $request->title,
            'link' => $request->link,
            'image' => $imagenBase64
        ]);
        
        $movie->genres()->sync($request->genre_ids ?? []);

        return $movie;
    }

    public function destroy($id)
    {
        Movie::findOrFail($id)->delete();
        return response()->noContent();
    }

    public function update(Request $request, $id)
    {
        $movie = Movie::findOrFail($id);
        $movie->update($request->only(['title', 'link', 'image']));
        $movie->genres()->sync($request->genre_ids ?? []);
        return response()->json($movie);
    }
}
