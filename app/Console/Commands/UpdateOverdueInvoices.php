<?php
// Console command to automatically update overdue status for invoices
// Runs via cronjob to update pending invoices to overdue status when past due date
// Only processes invoices with 'pending' status that have passed their due_date

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UpdateOverdueInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:update-overdue
                           {--dry-run : Show what would be updated without making changes}
                           {--force : Run without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically update pending invoices to overdue status when past due date (cronjob)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $isForced = $this->option('force');

        $this->info('🔍 Checking for invoices that should be marked as overdue...');

        // Get all pending invoices that are past due date
        $overdueInvoices = Invoice::with(['user', 'semester', 'club'])
            ->where('status', 'pending')
            ->where('due_date', '<', Carbon::now())
            ->get();

        if ($overdueInvoices->isEmpty()) {
            $this->info('✅ No overdue invoices found to process.');
            return self::SUCCESS;
        }

        $this->info("📊 Found {$overdueInvoices->count()} overdue invoice(s) to update.");

        // Display invoices that will be marked as overdue
        $headers = ['Invoice Code', 'Member', 'Payment Name', 'Amount', 'Due Date', 'Days Overdue'];
        $rows = [];

        foreach ($overdueInvoices as $invoice) {
            $daysOverdue = Carbon::now()->diffInDays($invoice->due_date);
            $rows[] = [
                $invoice->invoice_code,
                Str::limit($invoice->user->name, 20),
                Str::limit($invoice->name, 25),
                number_format($invoice->total_amount, 0, ',', '.') . ' VND',
                $invoice->due_date->format('d/m/Y'),
                $daysOverdue . ' day(s)'
            ];
        }

        $this->table($headers, $rows);

        if ($isDryRun) {
            $this->warn('🧪 DRY RUN: No changes were made. Use --force to actually update these invoices.');
            return self::SUCCESS;
        }

        // Ask for confirmation unless forced
        if (!$isForced) {
            if (!$this->confirm('Do you want to mark these invoices as overdue?')) {
                $this->info('❌ Operation cancelled by user.');
                return self::SUCCESS;
            }
        }

        // Update the invoices to overdue status
        $updatedCount = 0;
        $errors = [];

        foreach ($overdueInvoices as $invoice) {
            try {
                $invoice->update(['status' => 'overdue']);
                $updatedCount++;

                $this->line("✅ Updated: {$invoice->invoice_code} - {$invoice->user->name}");

                // Log the update
                Log::info("Invoice auto-updated to overdue", [
                    'invoice_id' => $invoice->id,
                    'invoice_code' => $invoice->invoice_code,
                    'user_id' => $invoice->user_id,
                    'user_name' => $invoice->user->name,
                    'due_date' => $invoice->due_date,
                    'days_overdue' => Carbon::now()->diffInDays($invoice->due_date),
                    'updated_at' => now()
                ]);
            } catch (\Exception $e) {
                $errors[] = "Failed to update {$invoice->invoice_code}: " . $e->getMessage();
                $this->error("❌ Failed to update: {$invoice->invoice_code}");

                Log::error("Failed to auto-update invoice to overdue", [
                    'invoice_id' => $invoice->id,
                    'invoice_code' => $invoice->invoice_code,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Summary
        if ($updatedCount > 0) {
            $this->info("🎉 Successfully updated {$updatedCount} invoice(s) to overdue status.");
        }

        if (!empty($errors)) {
            $this->error("⚠️  Encountered " . count($errors) . " error(s):");
            foreach ($errors as $error) {
                $this->line("   • {$error}");
            }
        }

        // Display summary statistics
        $this->info("\n📈 Summary Statistics:");
        $totalPending = Invoice::where('status', 'pending')->count();
        $totalOverdue = Invoice::where('status', 'overdue')->count();
        $totalPaid = Invoice::where('status', 'paid')->count();

        $this->line("   • Pending invoices: {$totalPending}");
        $this->line("   • Overdue invoices: {$totalOverdue}");
        $this->line("   • Paid invoices: {$totalPaid}");

        return self::SUCCESS;
    }
}
