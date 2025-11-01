<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Link;
use App\Traits\FetchesImage;
use App\Rules\ImageOrBase64;

class LinkController extends Controller
{
    use FetchesImage;

    public function index(Request $request)
    {
        $q = $request->query('q');
        $genreId = $request->query('genre');
        $links = Link::when($q, fn($query) => $query->where('title', 'like', "%$q%"))
                        ->when($genreId, function ($query, $genreId) {
                            $query->whereHas('genres', function ($q) use ($genreId) {
                                $q->where('genres.id', $genreId);
                            });
                        })
                        ->with('genres')
                        ->orderBy('url')
                        ->get();
        return response()->json($links);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'url' => 'required|url|unique:links,url',
            'image' => ['nullable', new ImageOrBase64],
        ]);

        $imagenBase64 = $this->fetchImageBase64($request->image);

        $link = Link::create([
            'title' => $request->title,
            'url' => $request->url,
            'image' => $imagenBase64
        ]);

        $link->genres()->sync($request->genre_ids ?? []);

        return $link;
    }

    public function destroy($id)
    {
        Link::findOrFail($id)->delete();
        return response()->noContent();
    }

    public function update(Request $request, $id)
    {
        $link = Link::findOrFail($id);
        $link->update($request->only(['title', 'url', 'image']));
        $link->genres()->sync($request->genre_ids ?? []);
        return response()->json($link);
    }
}
