<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EventsSeeder extends Seeder
{
    public function run(): void
    {
        //
        // 1. Load type IDs and location IDs
        //
        $types = DB::table('events_type')->pluck('id', 'name')->toArray();
        $locations = DB::table('events_location')->pluck('id')->toArray();

        //
        // 2. Title groups mapped by type name
        //
        $typeTitles = [
            'Musical' => [
                'Summer Jazz Night', 'Acoustic Evening Live', 'Classical Harmony Concert',
                'Indie Vibes Showcase', 'Rock the Night Festival', 'Orchestral Gala Performance',
                'Symphony Under the Stars', 'Acoustic Guitar Live Session',
                'Music Fusion Experience', 'Live Band Spotlight',
            ],

            'Theatre' => [
                'Grand Theatre Premiere', 'Modern Stage Act', 'Broadway Classics Night',
                'The Royal Theatre Performance', 'Evening of Dramatic Art',
                'Curtain Rise Showcase', 'The Stage Masters Play',
                'Classic Storytelling Performance', 'Avant-Garde Theatre Night',
                'The Spotlight Playhouse',
            ],

            'Drama' => [
                'Dramatic Expressions Showcase', 'Heartfelt Stories Night',
                'Emotional Art Performance', 'Dramatic Theatre Evening',
                'The Art of Drama Festival', 'Community Drama Stage',
                'Actors Circle Live Drama', 'Rising Stars Drama Show',
                'Dramatic Tales Night', 'Intense Moments Playhouse',
            ],

            'Food Fair' => [
                'Global Street Food Festival', 'Taste of the City Market',
                'Culinary Delights Expo', 'Food Lovers Carnival',
                'International Food Bazaar', 'Gourmet Feast Weekend',
                'Street Flavors Pop-Up Fair', 'Food & Culture Fiesta',
                'Urban Food Experience', 'Chef’s Special Food Expo',
            ],

            'Sports Event' => [
                'Community Athletics Day', 'City Sports Championship',
                'Regional Fitness Challenge', 'Saturday Sports Carnival',
                'Youth Sports Festival', 'Annual Running Competition',
                'Local Team Tournament', 'Intercity Sports Meet',
                'Weekend Fitness Rally', 'Summer Sports Marathon',
            ],

            'Workshop' => [
                'Creative Skills Workshop', 'Hands-on DIY Training',
                'Weekend Productivity Bootcamp', 'Design & Craft Masterclass',
                'Digital Skills Learning Day', 'Intro to Painting Workshop',
                'Beginner Coding Bootcamp', 'Photography Essentials Workshop',
                'Art & Craft Creativity Lab', 'Writing Mastery Workshop',
            ],

            'Tech Conference' => [
                'Future Tech Summit', 'Global Developer Conference',
                'Cloud & Innovation Expo', 'Digital Transformation Forum',
                'AI & Data Science Congress', 'Modern Web Tech Conclave',
                'Industry Technology Meetup', 'Software Engineering Symposium',
                'IT Solutions Summit', 'Cybersecurity Tech Forum',
            ],

            'Festival' => [
                'Community Culture Festival', 'Spring Celebration Fair',
                'Arts & Music Fusion Fest', 'Seasonal City Festival',
                'International Culture Day', 'Family Fun Festival',
                'Lantern Light Celebration', 'Summer Holiday Festival',
                'Artisan Crafts Festival', 'Global Traditions Fair',
            ],

            'Tech Expo' => [
                'Innovation Tech Expo', 'Smart Devices Showcase',
                'Robotics & Automation Expo', 'Future Gadgets Exhibition',
                'AI Product Launch Expo', 'VR & AR Interactive Experience',
                'Tech Startup Innovation Hall', 'Mobile Technology Expo',
                'Smart Home Device Showcase', 'Emerging Tech Solutions Expo',
            ],

            'Developer Meetup' => [
                'Laravel Dev Meetup', 'Weekend Coding Gathering',
                'Open Source Community Meetup', 'Full-Stack Developer Hangout',
                'Web Tech Knowledge Swap', 'JavaScript Developers Meetup',
                'Engineering Roundtable Night', 'PHP Developer Community Event',
                'Coding Enthusiasts Meetup', 'Hackers Night Casual Meetup',
            ],
        ];

        //
        // 3. Realistic long descriptions
        //
        $descriptions = [
            'This event offers an immersive and engaging experience with a rich variety of activities, performances, and opportunities to connect with fellow attendees. Expect a vibrant, memorable environment.',
            'A well-curated gathering that blends entertainment, education, and unique hands-on experiences. Perfect for participants looking to explore something new and inspiring.',
            'A dynamic event designed to entertain and inform, featuring expert sessions, interactive elements, and engaging segments for all interests.',
            'A creative and energetic experience that showcases talent, innovation, and community spirit. Attendees will enjoy lively performances and meaningful interactions.',
            'This event brings together enthusiasts from all backgrounds to celebrate passion, creativity, and shared experiences in a welcoming atmosphere.',
        ];

        //
        // Prepare array for bulk insert
        //
        $events = [];
        $usedTitles = [];

        for ($i = 0; $i < 100; $i++) {

            // 1) Pick event TYPE
            $typeName = array_rand($typeTitles);
            $typeId = $types[$typeName];

            // 2) Pick a unique title from that type
            do {
                $title = $typeTitles[$typeName][array_rand($typeTitles[$typeName])]
                    .' '.rand(1, 999); // Add slight variation for uniqueness
            } while (in_array($title, $usedTitles));

            $usedTitles[] = $title;

            // 3) URL key
            $urlKey = Str::slug($title, '_').'_'.Str::random(4);

            // 4) Time range
            $start = now()
                ->addDays(rand(1, 90))
                ->setTime(rand(8, 20), [0, 15, 30, 45][rand(0, 3)], 0);

            $end = (clone $start)->addHours(rand(1, 6));

            // 5) Insert row
            $events[] = [
                'title' => $title,
                'url_key' => $urlKey,
                'description' => $descriptions[array_rand($descriptions)],
                'start_time' => $start,
                'end_time' => $end,
                'location_id' => $locations[array_rand($locations)],
                'type_id' => $typeId,
                'capacity' => rand(10, 200),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        //
        // Insert all events
        //
        DB::table('events')->insert($events);
    }
}
