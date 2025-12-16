<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventsTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Musical',
            'Theatre',
            'Drama',
            'Food Fair',
            'Sports Event',
            'Workshop',
            'Tech Conference',
            'Festival',
            'Tech Expo',
            'Developer Meetup',
        ];

        foreach ($types as $type) {
            DB::table('events_type')->insert([
                'name' => $type,
            ]);
        }
    }
}
