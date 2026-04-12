<?php

namespace App\Console\Commands;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Journal;
use App\Models\Payment;
use App\Services\JournalService;
use Illuminate\Console\Command;

class RegenerateJournals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounting:regenerate-journals {--force : Record even if journal might exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate missing journals for approved invoices, payments, and expenses';

    protected $journalService;

    public function __construct(JournalService $journalService)
    {
        parent::__construct();
        $this->journalService = $journalService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');

        $this->info("Starting journal regeneration...");

        // 1. Sales Invoices
        $this->info("Processing Sales Invoices...");
        $salesInvoices = Invoice::where('tipe_invoice', 'Sales')
            ->where('status_dok', 'Approved')
            ->get();

        foreach ($salesInvoices as $inv) {
            $hasJournal = Journal::where('nomor_referensi', $inv->nomor_invoice)->exists();
            if (!$hasJournal || $force) {
                $this->journalService->recordSalesInvoice($inv->fresh(['items.product']));
                $this->line("Recorded Sales Journal for: {$inv->nomor_invoice}");
            }
        }

        // 2. Purchase Invoices
        $this->info("Processing Purchase Invoices...");
        $purchaseInvoices = Invoice::where('tipe_invoice', 'Purchase')
            ->where('status_dok', 'Approved')
            ->get();

        foreach ($purchaseInvoices as $inv) {
            $hasJournal = Journal::where('nomor_referensi', $inv->nomor_invoice)->exists();
            if (!$hasJournal || $force) {
                $this->journalService->recordPurchaseInvoice($inv->fresh(['items.product']));
                $this->line("Recorded Purchase Journal for: {$inv->nomor_invoice}");
            }
        }

        // 3. Payments
        $this->info("Processing Payments...");
        $payments = Payment::with('invoice')->get();

        foreach ($payments as $pay) {
            if (!$pay->invoice) {
                $this->warn("Payment {$pay->nomor_pembayaran} has no associated invoice. Skipping.");
                continue;
            }
            $hasJournal = Journal::where('nomor_referensi', $pay->nomor_pembayaran)->exists();
            if (!$hasJournal || $force) {
                $this->journalService->recordPayment($pay);
                $this->line("Recorded Payment Journal for: {$pay->nomor_pembayaran}");
            }
        }

        // 4. Expenses
        $this->info("Processing Expenses...");
        $expenses = Expense::all();

        foreach ($expenses as $exp) {
            $hasJournal = Journal::where('nomor_referensi', "EXP-{$exp->id}")->exists();
            if (!$hasJournal || $force) {
                $this->journalService->recordExpense($exp);
                $this->line("Recorded Expense Journal for ID: {$exp->id}");
            }
        }

        $this->info("Journal regeneration completed!");
    }
}
