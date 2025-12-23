<?php

namespace App\GraphQL\Queries;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class EventImagesQuery
{
    /**
     * @return array<int, string>
     */
    public function resolve(): array
    {
        return Cache::remember('event_images.random_six', now()->addHour(), function (): array {
            $files = Storage::disk('public')->allFiles('events');
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];

            return collect($files)
                ->filter(function (string $path) use ($allowedExtensions): bool {
                    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

                    return $extension !== '' && in_array($extension, $allowedExtensions, true);
                })
                ->shuffle()
                ->take(6)
                ->values()
                ->map(fn (string $path): string => Storage::url($path))
                ->all();
        });
    }
}
