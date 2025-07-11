<?php

// Console command to automatically complete events that have ended
// Runs via cronjob to update active events to completed status when their end time has passed
// Only processes events with 'active' status that have ended based on event_date and slot end_time

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CompleteEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:complete
                           {--dry-run : Show what would be updated without making changes}
                           {--force : Run without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically complete events that have ended (cronjob)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $isForced = $this->option('force');

        $this->info('🔍 Checking for events that should be completed...');

        // Get all active events with their slots
        $activeEvents = Event::with('slot')
            ->where('status', 'active')
            ->whereNotNull('event_date')
            ->whereNotNull('slot_id')
            ->get();

        if ($activeEvents->isEmpty()) {
            $this->info('✅ No active events found to process.');
            return self::SUCCESS;
        }

        $this->info("📊 Found {$activeEvents->count()} active event(s) to check.");

        $eventsToComplete = [];
        $now = Carbon::now();

        foreach ($activeEvents as $event) {
            if ($event->hasEventEnded()) {
                $eventsToComplete[] = $event;
            }
        }

        if (empty($eventsToComplete)) {
            $this->info('✅ No events have ended yet. All active events are still in progress or scheduled for the future.');
            return self::SUCCESS;
        }

        $this->info("🎯 Found " . count($eventsToComplete) . " event(s) that have ended:");

        // Display events that will be completed
        $headers = ['Event Code', 'Event Name', 'Event Date', 'End Time', 'Status'];
        $rows = [];

        foreach ($eventsToComplete as $event) {
            $rows[] = [
                $event->event_code,
                Str::limit($event->name, 30),
                $event->formatted_event_date,
                $event->slot->end_time ?? 'N/A',
                $event->status_label
            ];
        }

        $this->table($headers, $rows);

        if ($isDryRun) {
            $this->warn('🧪 DRY RUN: No changes were made. Use --force to actually complete these events.');
            return self::SUCCESS;
        }

        // Ask for confirmation unless forced
        if (!$isForced) {
            if (!$this->confirm('Do you want to complete these events?')) {
                $this->info('❌ Operation cancelled by user.');
                return self::SUCCESS;
            }
        }

        // Complete the events
        $completedCount = 0;
        $errors = [];

        foreach ($eventsToComplete as $event) {
            try {
                $event->update(['status' => 'completed']);
                $completedCount++;

                $this->line("✅ Completed: {$event->event_code} - {$event->name}");

                // Log the completion
                Log::info("Event auto-completed", [
                    'event_id' => $event->id,
                    'event_code' => $event->event_code,
                    'event_name' => $event->name,
                    'event_date' => $event->event_date,
                    'completed_at' => now()
                ]);
            } catch (\Exception $e) {
                $errors[] = "Failed to complete {$event->event_code}: " . $e->getMessage();
                $this->error("❌ Failed to complete: {$event->event_code}");

                Log::error("Failed to auto-complete event", [
                    'event_id' => $event->id,
                    'event_code' => $event->event_code,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Summary
        if ($completedCount > 0) {
            $this->info("🎉 Successfully completed {$completedCount} event(s).");
        }

        if (!empty($errors)) {
            $this->error("⚠️  Encountered " . count($errors) . " error(s):");
            foreach ($errors as $error) {
                $this->line("   • {$error}");
            }
        }

        return self::SUCCESS;
    }
}
