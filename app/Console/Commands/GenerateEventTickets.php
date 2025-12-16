<?php

namespace App\Console\Commands;

use App\Models\Events;
use App\Models\EventTicket;
use Illuminate\Console\Command;

class GenerateEventTickets extends Command
{
    protected $signature = 'tickets:generate {--refresh : Delete old tickets before regenerating}';

    protected $description = 'Generate event_ticket entries based on event capacity';

    /**
     * Generate tickets for all events, optionally refreshing existing entries.
     */
    public function handle(): int
    {
        $this->info("Starting ticket generation...\n");

        $events = Events::all();

        foreach ($events as $event) {
            // Count existing tickets
            $existingCount = EventTicket::where('event_id', $event->id)->count();

            // Optionally delete old tickets
            if ($this->option('refresh') && $existingCount > 0) {
                $this->warn("Refreshing tickets for {$event->title} (deleting {$existingCount})...");
                EventTicket::where('event_id', $event->id)->delete();
                $existingCount = 0;
            }

            // Prevent duplicates when not refreshing
            if ($existingCount >= $event->capacity) {
                $this->line("Skipping {$event->title}: tickets already generated ({$existingCount}/{$event->capacity}).");

                continue;
            }

            // How many tickets we need
            $needed = $event->capacity - $existingCount;

            $this->info("Generating {$needed} tickets for event: {$event->title}");
            $this->output->progressStart($needed);

            // Generate missing tickets
            for ($i = 0; $i < $needed; $i++) {
                EventTicket::create([
                    'event_id' => $event->id,
                    'customer_id' => 0,
                    'status' => 'open',
                    'hash_key' => '',
                ]);

                $this->output->progressAdvance();
            }

            $this->output->progressFinish();
            $this->info("Finished event {$event->id}\n");
        }

        $this->info('All ticket generation completed!');

        return self::SUCCESS;
    }
}
