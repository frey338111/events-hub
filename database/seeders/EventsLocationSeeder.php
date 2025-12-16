<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventsLocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            'New York',
            'London',
            'Tokyo',
            'Toronto',
            'Sydney',
            'Berlin',
            'Amsterdam',
            'Singapore',
            'Paris',
            'Hong Kong',
        ];

        foreach ($locations as $location) {
            DB::table('events_location')->insert([
                'name' => $location,
            ]);
        }
    }
}
