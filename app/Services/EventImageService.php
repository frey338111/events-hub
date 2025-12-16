<?php

namespace App\Services;

use App\Models\Events;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class EventImageService
{
    /**
     * Handle upload + thumbnail creation for an event image.
     */
    public function upload(Events $event, UploadedFile $file): array
    {
        // Remove existing files
        $this->deleteExistingImages($event);

        $filename = time().'_'.$file->getClientOriginalName();

        // Save original
        $file->storeAs('events', $filename, 'public');

        // Generate thumbnail
        $thumbnailFilename = 'thumb_'.$filename;
        Storage::disk('public')->makeDirectory('events/thumbs');

        $thumbnailPath = storage_path('app/public/events/thumbs/'.$thumbnailFilename);

        $manager = new ImageManager(new \Intervention\Image\Drivers\Gd\Driver);
        $manager->read($file->getPathname())
            ->scale(300, 300)
            ->save($thumbnailPath);

        return [
            'events_image' => $filename,
            'events_thumbnail' => $thumbnailFilename,
        ];
    }

    /**
     * Delete old image + thumbnail.
     */
    public function deleteExistingImages(Events $event): void
    {
        if ($event->events_image && Storage::disk('public')->exists('events/'.$event->events_image)) {
            Storage::disk('public')->delete('events/'.$event->events_image);
        }

        if ($event->events_thumbnail && Storage::disk('public')->exists('events/thumbs/'.$event->events_thumbnail)) {
            Storage::disk('public')->delete('events/thumbs/'.$event->events_thumbnail);
        }
    }
}
