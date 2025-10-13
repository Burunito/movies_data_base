<?php

namespace App\Http\Controllers;

use App\Models\Actor;
use App\Traits\FetchesImage;
use Illuminate\Http\Request;
use App\Rules\ImageOrBase64;

class ActorController extends Controller
{
    use FetchesImage;

    public function index(Request $request)
    {
        $q = $request->query('q');
        $genreId = $request->query('genre');
        $actors = Actor::when($q, fn($query) => $query->where('name', 'like', "%$q%"))
                        ->when($genreId, function ($query, $genreId) {
                            $query->whereHas('genres', function ($q) use ($genreId) {
                                $q->where('genres.id', $genreId);
                            });
                        })
                        ->with('genres')
                        ->orderBy('name')
                        ->get();
        return response()->json($actors);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required',
            'link'  => 'required|url|unique:actors',
            'image' => ['nullable', new ImageOrBase64],
        ]);

        $imagenBase64 = $this->fetchImageBase64($request->image);

        $actor = Actor::create([
            'name' => $request->name,
            'link' => $request->link,
            'image' => $imagenBase64
        ]);

        $actor->genres()->sync($request->genre_ids ?? []);

        return $actor;
    }

    public function destroy(Actor $actor)
    {
        $actor->delete();
        return response()->json(['ok' => true]);
    }

    public function update(Request $request, $id)
    {
        $actor = Actor::findOrFail($id);
        $actor->update($request->only(['name', 'link', 'image']));
        $actor->genres()->sync($request->genre_ids ?? []);
        return response()->json($actor);
    }
}
