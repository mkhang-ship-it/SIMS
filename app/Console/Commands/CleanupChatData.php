<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Message;
use App\Models\Chat;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class CleanupChatData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:cleanup 
                            {--days=30 : Number of days to keep deleted messages}
                            {--files=90 : Number of days to keep orphaned files}
                            {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old chat data including deleted messages and orphaned files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $daysToKeep = $this->option('days');
        $fileDaysToKeep = $this->option('files');
        $dryRun = $this->option('dry-run');

        $this->info('Starting chat data cleanup...');

        // Clean up old deleted messages
        $this->cleanupDeletedMessages($daysToKeep, $dryRun);

        // Clean up orphaned files
        $this->cleanupOrphanedFiles($fileDaysToKeep, $dryRun);

        // Clean up empty chats
        $this->cleanupEmptyChats($dryRun);

        $this->info('Chat cleanup completed!');
    }

    /**
     * Clean up soft-deleted messages older than specified days
     */
    private function cleanupDeletedMessages(int $days, bool $dryRun)
    {
        $this->info("Cleaning up deleted messages older than {$days} days...");

        $cutoffDate = Carbon::now()->subDays($days);
        
        $query = Message::onlyTrashed()
            ->where('deleted_at', '<', $cutoffDate);

        $count = $query->count();

        if ($count > 0) {
            if ($dryRun) {
                $this->warn("Would permanently delete {$count} messages");
            } else {
                // Get file paths before deleting
                $filePaths = $query->whereNotNull('file_path')
                    ->pluck('file_path')
                    ->toArray();

                // Delete files from storage
                foreach ($filePaths as $filePath) {
                    if (Storage::disk('public')->exists($filePath)) {
                        Storage::disk('public')->delete($filePath);
                    }
                }

                // Permanently delete messages
                $query->forceDelete();
                $this->info("Permanently deleted {$count} messages and their files");
            }
        } else {
            $this->info('No old deleted messages found');
        }
    }

    /**
     * Clean up orphaned files (files not referenced by any message)
     */
    private function cleanupOrphanedFiles(int $days, bool $dryRun)
    {
        $this->info("Cleaning up orphaned files older than {$days} days...");

        $chatFilesPath = 'chat_files';
        $avatarsPath = 'chat_avatars';

        if (!Storage::disk('public')->exists($chatFilesPath)) {
            $this->info('No chat files directory found');
            return;
        }

        $allFiles = Storage::disk('public')->allFiles($chatFilesPath);
        $referencedFiles = Message::whereNotNull('file_path')
            ->pluck('file_path')
            ->toArray();

        $orphanedFiles = array_diff($allFiles, $referencedFiles);
        $cutoffDate = Carbon::now()->subDays($days);

        $oldOrphanedFiles = [];
        foreach ($orphanedFiles as $file) {
            $lastModified = Carbon::createFromTimestamp(
                Storage::disk('public')->lastModified($file)
            );
            
            if ($lastModified->lt($cutoffDate)) {
                $oldOrphanedFiles[] = $file;
            }
        }

        if (count($oldOrphanedFiles) > 0) {
            if ($dryRun) {
                $this->warn("Would delete " . count($oldOrphanedFiles) . " orphaned files");
                foreach ($oldOrphanedFiles as $file) {
                    $this->line("  - {$file}");
                }
            } else {
                foreach ($oldOrphanedFiles as $file) {
                    Storage::disk('public')->delete($file);
                }
                $this->info("Deleted " . count($oldOrphanedFiles) . " orphaned files");
            }
        } else {
            $this->info('No old orphaned files found');
        }
    }

    /**
     * Clean up empty chats (chats with no messages and no users)
     */
    private function cleanupEmptyChats(bool $dryRun)
    {
        $this->info('Cleaning up empty chats...');

        $emptyChats = Chat::whereDoesntHave('messages')
            ->whereDoesntHave('users')
            ->get();

        if ($emptyChats->count() > 0) {
            if ($dryRun) {
                $this->warn("Would delete {$emptyChats->count()} empty chats");
            } else {
                foreach ($emptyChats as $chat) {
                    // Delete chat avatar if exists
                    if ($chat->avatar && Storage::disk('public')->exists($chat->avatar)) {
                        Storage::disk('public')->delete($chat->avatar);
                    }
                    
                    $chat->delete();
                }
                $this->info("Deleted {$emptyChats->count()} empty chats");
            }
        } else {
            $this->info('No empty chats found');
        }
    }
}
