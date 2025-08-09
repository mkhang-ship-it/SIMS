<?php
// Created: Maintenance command for enabling/disabling maintenance mode via CLI

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;
use Illuminate\Support\Str;

class MaintenanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenance {action : on|off|status} {--message= : Custom maintenance message} {--token= : Custom bypass token}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage maintenance mode for the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        $message = $this->option('message');
        $token = $this->option('token');

        switch ($action) {
            case 'on':
                $this->enableMaintenance($message, $token);
                break;
            case 'off':
                $this->disableMaintenance();
                break;
            case 'status':
                $this->showStatus();
                break;
            default:
                $this->error('Invalid action. Use: on, off, or status');
                return 1;
        }

        return 0;
    }

    private function enableMaintenance($message = null, $token = null)
    {
        // Check if maintenance mode is already enabled
        $currentMode = Setting::getValue(null, 'maintenance_mode');

        // Enable maintenance mode
        Setting::updateOrCreate(
            ['club_id' => null, 'key' => 'maintenance_mode', 'is_admin' => 'central'],
            ['value' => '1']
        );

        // Set custom message if provided
        if ($message) {
            Setting::updateOrCreate(
                ['club_id' => null, 'key' => 'maintenance_message', 'is_admin' => 'central'],
                ['value' => $message]
            );
        }

        // Always generate new bypass token when enabling maintenance mode
        $bypassToken = $token ?: Str::random(32);
        Setting::updateOrCreate(
            ['club_id' => null, 'key' => 'maintenance_bypass_token', 'is_admin' => 'central'],
            ['value' => $bypassToken]
        );

        if ($currentMode === '1') {
            $this->info('Maintenance mode is already enabled. Token has been refreshed!');
        } else {
            $this->info('Maintenance mode enabled successfully!');
        }
        $this->info('Bypass token: ' . $bypassToken);
        $this->info('Bypass URL: ' . url('/') . '/bypass/' . $bypassToken);
        $this->info('Token expires after 10 minutes of inactivity.');
    }

    private function disableMaintenance()
    {
        // Disable maintenance mode
        Setting::updateOrCreate(
            ['club_id' => null, 'key' => 'maintenance_mode', 'is_admin' => 'central'],
            ['value' => '0']
        );

        $this->info('Maintenance mode disabled successfully!');
    }

    private function showStatus()
    {
        $maintenanceMode = Setting::getValue(null, 'maintenance_mode');
        $bypassToken = Setting::getValue(null, 'maintenance_bypass_token');
        $message = Setting::getValue(null, 'maintenance_message');

        $this->info('Maintenance Mode Status:');
        $this->line('Enabled: ' . ($maintenanceMode === '1' ? 'Yes' : 'No'));

        if ($maintenanceMode === '1') {
            $this->line('Bypass Token: ' . ($bypassToken ?: 'Not set'));
            $this->line('Bypass URL: ' . ($bypassToken ? url('/') . '/bypass/' . $bypassToken : 'Not available'));
            $this->line('Token Expiry: 10 minutes of inactivity');
            $this->line('Custom Message: ' . ($message ?: 'Using default message'));
        }
    }
}
