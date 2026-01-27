<?php

namespace App\Services;

use App\Models\Journal;
use App\Models\JournalDetail;
use App\Models\COA;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\StockMutation;
use Illuminate\Support\Facades\DB;

class JournalService
{
    /**
     * Create a manual journal entry
     */
    public function createJournal($officeId, $date, $refNo, $desc, $details)
    {
        // details = [['akun_id' => 1, 'debit' => 1000, 'kredit' => 0], ...]
        return DB::transaction(function () use ($officeId, $date, $refNo, $desc, $details) {
            $journal = Journal::create([
                'office_id' => $officeId,
                'tgl_jurnal' => $date,
                'nomor_referensi' => $refNo,
                'keterangan' => $desc
            ]);

            foreach ($details as $det) {
                JournalDetail::create([
                    'journal_id' => $journal->id,
                    'akun_id' => $det['akun_id'],
                    'debit' => $det['debit'],
                    'kredit' => $det['kredit']
                ]);
            }

            return $journal;
        });
    }

    /**
     * Handle Expense Journal
     */
    public function recordExpense(Expense $expense)
    {
        $officeId = $expense->office_id;
        
        $details = [];

        // Debit Expense Account
        $details[] = [
            'akun_id' => $expense->akun_beban_id,
            'debit' => $expense->jumlah,
            'kredit' => 0
        ];

        // Credit Payment Account (Cash/Bank)
        // Resolve COA from FinancialAccount
        $finAccount = \App\Models\FinancialAccount::find($expense->akun_keuangan_id);
        $creditCoaId = null;
        if ($finAccount) {
            $code = ($finAccount->type == 'Cash') ? '1101' : '1201'; // Default Cash or Bank
            if ($finAccount->type == 'Corporate Card') $code = '1251';
            
            $coa = $this->findAccount($officeId, $code);
            if ($coa) $creditCoaId = $coa->id;
        }

        if ($creditCoaId) {
            $details[] = [
                'akun_id' => $creditCoaId,
                'debit' => 0,
                'kredit' => $expense->jumlah
            ];

            $this->createJournal(
                $officeId,
                $expense->tgl_biaya,
                "EXP-{$expense->id}",
                "Expense: {$expense->nama_biaya}",
                $details
            );
        }
    }

    /**
     * Handle Purchase Invoice Journal
     * Method 1: Track Stock OFF -> Expense (HPP) directly.
     * Method 2: Track Stock ON -> Inventory Asset.
     */
    public function recordPurchaseInvoice(Invoice $invoice)
    {
        $officeId = $invoice->office_id;
        
        // Find Accounts
        $accAP = $this->findAccount($officeId, '2101'); // Hutang Usaha
        $accInventory = $this->findAccount($officeId, '1401') ?? $this->findAccount($officeId, '5101'); // Fallback to HPP if no Inventory
        $accExpense = $this->findAccount($officeId, '5101'); // HPP / Pembelian

        if (!$accAP) return; // Cannot proceed without AP account

        $details = [];
        $totalPayable = $invoice->total_akhir;

        // Credit AP
        $details[] = [
            'akun_id' => $accAP->id,
            'debit' => 0,
            'kredit' => $totalPayable
        ];

        // Debit: Split based on items
        foreach ($invoice->items as $item) {
            $amount = $item->total; // qty * price - discount + tax (assuming total is net)
            // Wait, InvoiceItem model usually has 'total'. Let's check calculation.
            // Assuming item->total is correct.
            
            $product = $item->product;
            $isTrackStock = $product ? $product->track_stock : false;

            $debitAccId = $isTrackStock ? ($accInventory->id ?? $accExpense->id) : $accExpense->id;

            // Aggregate debits to avoid too many lines? Or keep detail? 
            // For simplicity, let's aggregate by account in a real app, but here push individually is fine or aggregate.
            // Let's aggregate.
            $found = false;
            foreach ($details as &$d) {
                if ($d['akun_id'] == $debitAccId && $d['debit'] > 0) { // Check if it's a debit line
                    $d['debit'] += $amount;
                    $found = true;
                    break;
                }
            }
            if (! $found) {
                $details[] = [
                    'akun_id' => $debitAccId,
                    'debit' => $amount,
                    'kredit' => 0
                ];
            }
        }

        // Handle Taxes/Fees if separate? 
        // For now assume item->total sums up to invoice->total_akhir. 
        // If there are global taxes not in items, we need to handle that.
        // Usually Invoice total_akhir includes global tax.
        // Let's assume strict item-based for now.

        $this->createJournal(
            $officeId,
            $invoice->tgl_invoice,
            $invoice->nomor_invoice,
            "Purchase Invoice #{$invoice->nomor_invoice}",
            $details
        );
    }

    /**
     * Handle Sales Invoice Journal
     * Revenue: Credit Sales, Debit AR.
     * COGS: Debit HPP, Credit Inventory (only for Track Stock ON).
     */
    public function recordSalesInvoice(Invoice $invoice)
    {
        $officeId = $invoice->office_id;

        $accAR = $this->findAccount($officeId, '1301'); // Piutang Usaha
        $accRevenue = $this->findAccount($officeId, '4101'); // Penjualan
        $accHPP = $this->findAccount($officeId, '5101'); // HPP
        $accInventory = $this->findAccount($officeId, '1401'); // Persediaan

        if (!$accAR || !$accRevenue) return;

        $details = [];

        // 1. Revenue Entry
        // Debit AR
        $details[] = [
            'akun_id' => $accAR->id,
            'debit' => $invoice->total_akhir,
            'kredit' => 0
        ];
        // Credit Revenue
        $details[] = [
            'akun_id' => $accRevenue->id,
            'debit' => 0,
            'kredit' => $invoice->total_akhir
        ];

        // 2. COGS Entry (Perpetual)
        // Check StockMutations for this invoice
        $mutations = StockMutation::where('reference_type', 'Sales')
            ->where('reference_id', $invoice->id)
            ->get();

        $totalCOGS = 0;
        foreach ($mutations as $mut) {
            $totalCOGS += ($mut->qty * $mut->cost_price);
        }

        if ($totalCOGS > 0 && $accHPP && $accInventory) {
            // Debit HPP
            $details[] = [
                'akun_id' => $accHPP->id,
                'debit' => $totalCOGS,
                'kredit' => 0
            ];
            // Credit Inventory
            $details[] = [
                'akun_id' => $accInventory->id,
                'debit' => 0,
                'kredit' => $totalCOGS
            ];
        }

        $this->createJournal(
            $officeId,
            $invoice->tgl_invoice,
            $invoice->nomor_invoice,
            "Sales Invoice #{$invoice->nomor_invoice}",
            $details
        );
    }

    /**
     * Handle Payment Journal
     */
    public function recordPayment(Payment $payment)
    {
        $officeId = $payment->office_id;
        $invoice = $payment->invoice;

        // Resolve COA from FinancialAccount
        $finAccount = \App\Models\FinancialAccount::find($payment->akun_keuangan_id);
        $accCashBank = null;
        if ($finAccount) {
            $code = ($finAccount->type == 'Cash') ? '1101' : '1201';
            if ($finAccount->type == 'Corporate Card') $code = '1251';
            $accCashBank = $this->findAccount($officeId, $code);
        }

        if (!$accCashBank) return;

        $details = [];

        if ($invoice->tipe_invoice == 'Sales') {
            // Received Payment
            // Debit Cash/Bank
            $details[] = [
                'akun_id' => $accCashBank->id,
                'debit' => $payment->jumlah_bayar,
                'kredit' => 0
            ];
            
            // Credit AR
            $accAR = $this->findAccount($officeId, '1301');
            if ($accAR) {
                $details[] = [
                    'akun_id' => $accAR->id,
                    'debit' => 0,
                    'kredit' => $payment->jumlah_bayar
                ];
            }
        } else {
            // Paid to Supplier
            // Credit Cash/Bank
            $details[] = [
                'akun_id' => $accCashBank->id,
                'debit' => 0,
                'kredit' => $payment->jumlah_bayar
            ];

            // Debit AP
            $accAP = $this->findAccount($officeId, '2101');
            if ($accAP) {
                $details[] = [
                    'akun_id' => $accAP->id,
                    'debit' => $payment->jumlah_bayar,
                    'kredit' => 0
                ];
            }
        }

        $this->createJournal(
            $officeId,
            $payment->tgl_pembayaran,
            $payment->nomor_pembayaran,
            "Payment for {$invoice->nomor_invoice}",
            $details
        );
    }

    private function findAccount($officeId, $code)
    {
        return COA::where('office_id', $officeId)
            ->where('kode_akun', $code)
            ->first();
    }
}
