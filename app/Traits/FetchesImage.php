<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait FetchesImage
{
    /**
     * Fetch an image from a URL and return a Base64 string.
     * Returns null if fetching fails.
     */
    public function fetchImageBase64(?string $input): ?string
    {
        if (!$input) return null;

        // If input already looks like a base64 string
        if (str_starts_with($input, 'data:image/')) {
            return $input; // already base64
        }
        // Otherwise, try to fetch from URL
        try {
            $response = Http::withOptions(['verify' => false])->get($input);

            if ($response->ok()) {
                $mimeType = $response->header('Content-Type'); // e.g., image/jpeg
                $data = base64_encode($response->body());
                return "data:$mimeType;base64,$data";
            }
        } catch (\Exception $e) {
            // Could not fetch, return null
            return null;
        }

        return null;
    }
}
