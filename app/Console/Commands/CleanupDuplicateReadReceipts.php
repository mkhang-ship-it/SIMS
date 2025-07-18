<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MessageReadReceipt;
use Illuminate\Support\Facades\DB;

class CleanupDuplicateReadReceipts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:cleanup-duplicate-read-receipts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up duplicate message read receipts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cleanup of duplicate read receipts...');

        // Find and remove duplicate read receipts, keeping only the latest one
        $duplicatesRemoved = DB::statement("
            DELETE r1 FROM message_read_receipts r1
            INNER JOIN message_read_receipts r2 
            WHERE r1.message_id = r2.message_id 
                AND r1.user_id = r2.user_id 
                AND r1.created_at < r2.created_at
        ");

        $this->info("Cleanup completed. Duplicate read receipts have been removed.");

        return Command::SUCCESS;
    }
}
