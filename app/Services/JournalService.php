<?php

namespace App\Services;

use App\Models\COA;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Journal;
use App\Models\JournalDetail;
use App\Models\Payment;
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
                'keterangan' => $desc,
            ]);

            foreach ($details as $det) {

                $nomorJournal = $this->generateJournalNumber($det['akun_id'], $date);

                JournalDetail::create([
                    'nomor_journal' => $nomorJournal,
                    'journal_id' => $journal->id,
                    'akun_id' => $det['akun_id'],
                    'debit' => $det['debit'],
                    'kredit' => $det['kredit'],
                ]);
            }

            return $journal;
        });
    }

    private function generateJournalNumber($accountId, $date)
    {
        $account = COA::find($accountId);
        $code = $this->getAccountCode($account->nama_akun);
        $year = \Carbon\Carbon::parse($date)->format('Y');

        $lastNumber = JournalDetail::whereHas('journal', function ($q) use ($year) {
            $q->whereYear('tgl_jurnal', $year);
        })
            ->where('akun_id', $accountId)
            ->orderByDesc('id')
            ->value('nomor_journal');

        $lastSequence = 0;

        if ($lastNumber) {
            $parts = explode('/', $lastNumber);
            $lastSequence = intval(end($parts));
        }

        $newSequence = str_pad($lastSequence + 1, 4, '0', STR_PAD_LEFT);

        return "{$code}/{$year}/{$newSequence}";
    }

    private function getAccountCode($accountName)
    {
        $map = [
            'Kas' => 'KAS',
            'Bank' => 'BAN',
            'Rekening Bersama Digital Payment Paper.id' => 'RDP',
            'Piutang Usaha' => 'PIU',
            'Persediaan' => 'PER',
            'Persediaan dalam Perjalanan' => 'PDP',
            'Persediaan Konsinyasi' => 'PKS',
            'PPN Masukan' => 'PPN-M',
            'PPH Pasal 23 Dibayar Dimuka' => 'PPH23-DM',
            'Bangunan' => 'BGN',
            'Peralatan' => 'ALT',
            'Kendaraan' => 'KND',
            'Akumulasi Penyusutan Bangunan' => 'AKM-BGN',
            'Akumulasi Penyusutan Peralatan' => 'AKM-ALT',
            'Akumulasi Penyusutan Kendaraan' => 'AKM-KND',
            'Hutang Usaha' => 'HUT',
            'Pendapatan Diterima Di Muka' => 'PDM',
            'Penjualan dimuka in transit' => 'PJT',
            'Hutang PPH Pasal 21' => 'PPH21',
            'Hutang PPH Pasal 22' => 'PPH22',
            'Hutang PPH Pasal 4(2)' => 'PPH42',
            'Hutang PPH Pasal 25' => 'PPH25',
            'PPN Keluaran' => 'PPN-K',
            'Hutang PPN' => 'HUT-PPN',
            'Hutang Bank' => 'HUT-BANK',
            'Modal Disetor' => 'MOD',
            'Dividen' => 'DIV',
            'Saldo Ekuitas Awal' => 'SEA',
            'Laba Ditahan' => 'LD',
            'Laba Tahun Berjalan' => 'LTB',
            'Penjualan Umum' => 'PJL-UM',
            'Pendapatan Jasa' => 'PDJ',
            'Penjualan Produk' => 'PJL-PROD',
            'Pendapatan Pengiriman' => 'PDK',
            'Diskon Penjualan' => 'DSK-PJL',
            'Retur Penjualan' => 'RTR-PJL',
            'Harga Pokok Penjualan' => 'HPP',
            'Beban Pengiriman' => 'BBN-KRM',
            'Beban Pembelian' => 'BBN-BELI',
            'Diskon Pembelian' => 'DSK-BELI',
            'Retur Pembelian' => 'RTR-BELI',
            'Beban Gaji Operasional' => 'GAJI-OPS',
            'Beban Gaji Administrasi' => 'GAJI-ADM',
            'Biaya Pencairan Digital Payment' => 'BYR-DP',
            'Biaya Pembayaran Keluar' => 'BYR-KLR',
            'Beban Listrik dan Air' => 'LSTR-AIR',
            'Beban Kendaraan dan Transportasi' => 'KND-TRP',
            'Beban Komunikasi' => 'KMN',
            'Beban Perlengkapan Kantor' => 'ATK',
            'Beban Komisi Penjualan' => 'KMS-PJL',
            'Beban Entertainment' => 'ENT',
            'Beban Iklan dan Promosi' => 'IKL',
            'Beban Perbaikan dan Pemeliharaan' => 'PRB',
            'Beban Sewa' => 'SWA',
            'Beban Asuransi' => 'ASR',
            'Beban Penyesuaian Persediaan' => 'ADJ-PER',
            'Beban Cacat Produksi' => 'CACAT',
            'Beban Perijinan dan Lisensi' => 'LIS',
            'Beban Piutang Tak Tertagih' => 'BAD-DEBT',
            'Beban Penyusutan Bangunan' => 'DEP-BGN',
            'Beban Penyusutan Peralatan' => 'DEP-ALT',
            'Beban Penyusutan Kendaraan' => 'DEP-KND',
            'Pendapatan Lain-lain' => 'PDL',
            'Pendapatan Bunga' => 'PDB',
            'Keuntungan dari Selisih Kurs' => 'GAIN-KURS',
            'Keuntungan Dari Penjualan Aktiva Tetap' => 'GAIN-AT',
            'Beban Lain-lain' => 'BBN-LAIN',
            'Beban Bunga' => 'BBN-BNG',
            'Beban Administrasi Bank' => 'ADM-BANK',
            'Kerugian Selisih Kurs' => 'LOSS-KURS',
            'Kerugian Dari Penjualan Aktiva Tetap' => 'LOSS-AT',
            'Beban Pajak Penghasilan' => 'PPH-BBN',
        ];

        return $map[$accountName] ?? 'GEN';
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
            'debit' => $expense->jumlah ?: 0,
            'kredit' => 0,
        ];

        // Credit Payment Account (Cash/Bank)
        // Resolve COA from FinancialAccount
        $finAccount = \App\Models\FinancialAccount::find($expense->akun_keuangan_id);
        $creditCoaId = null;
        if ($finAccount) {
            $code = ($finAccount->type == 'Cash') ? '1101' : '1201'; // Default Cash or Bank
            if ($finAccount->type == 'Corporate Card') {
                $code = '1251';
            }

            $coa = $this->findAccount($officeId, $code);
            if ($coa) {
                $creditCoaId = $coa->id;
            }
        }

        if ($creditCoaId) {
            $details[] = [
                'akun_id' => $creditCoaId,
                'debit' => 0,
                'kredit' => $expense->jumlah ?: 0,
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
        $accInventory = $this->findAccount($officeId, '1501') ?? $this->findAccount($officeId, '5101'); // Fallback to HPP if no Inventory
        $accExpense = $this->findAccount($officeId, '5101'); // HPP / Pembelian

        if (! $accAP) {
            return;
        } // Cannot proceed without AP account

        $details = [];
        $totalPayable = $invoice->total_akhir;

        // Credit AP
        $details[] = [
            'akun_id' => $accAP->id,
            'debit' => 0,
            'kredit' => $totalPayable,
        ];

        // Debit: Split based on items
        foreach ($invoice->items as $item) {
            $amount = $item->total ?: 0; // qty * price - discount + tax (assuming total is net)
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
                    'kredit' => 0,
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
        $accInventory = $this->findAccount($officeId, '1501'); // Persediaan

        if (! $accAR || ! $accRevenue) {
            return;
        }

        $details = [];

        // 1. Revenue Entry
        // Debit AR
        $details[] = [
            'akun_id' => $accAR->id,
            'debit' => $invoice->total_akhir ?: 0,
            'kredit' => 0,
        ];
        // Credit Revenue
        $details[] = [
            'akun_id' => $accRevenue->id,
            'debit' => 0,
            'kredit' => $invoice->total_akhir ?: 0,
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
                'kredit' => 0,
            ];
            // Credit Inventory
            $details[] = [
                'akun_id' => $accInventory->id,
                'debit' => 0,
                'kredit' => $totalCOGS,
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

        if (!$invoice) {
            return;
        }

        // Resolve COA from FinancialAccount
        $finAccount = \App\Models\FinancialAccount::find($payment->akun_keuangan_id);
        $accCashBank = null;
        if ($finAccount) {
            $code = ($finAccount->type == 'Cash') ? '1101' : '1201';
            if ($finAccount->type == 'Corporate Card') {
                $code = '1251';
            }
            $accCashBank = $this->findAccount($officeId, $code);
        }

        if (! $accCashBank) {
            return;
        }

        $details = [];

        if ($invoice->tipe_invoice == 'Sales') {
            // Received Payment
            // Debit Cash/Bank
            $details[] = [
                'akun_id' => $accCashBank->id,
                'debit' => $payment->jumlah_bayar ?: 0,
                'kredit' => 0,
            ];

            // Credit AR
            $accAR = $this->findAccount($officeId, '1301');
            if ($accAR) {
                $details[] = [
                    'akun_id' => $accAR->id,
                    'debit' => 0,
                    'kredit' => $payment->jumlah_bayar ?: 0,
                ];
            }
        } else {
            // Paid to Supplier
            // Credit Cash/Bank
            $details[] = [
                'akun_id' => $accCashBank->id,
                'debit' => 0,
                'kredit' => $payment->jumlah_bayar ?: 0,
            ];

            // Debit AP
            $accAP = $this->findAccount($officeId, '2101');
            if ($accAP) {
                $details[] = [
                    'akun_id' => $accAP->id,
                    'debit' => $payment->jumlah_bayar ?: 0,
                    'kredit' => 0,
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

    /**
     * Update existing payment journal
     */
    public function updatePaymentJournal(Payment $payment)
    {
        DB::transaction(function () use ($payment) {
            $oldRef = $payment->getOriginal('nomor_pembayaran');
            $journal = Journal::where('nomor_referensi', $oldRef)
                ->orWhere('nomor_referensi', $payment->nomor_pembayaran)
                ->orWhere('nomor_referensi', "PAY-{$payment->id}")
                ->first();

            if ($journal) {
                // Delete existing details and journal
                $journal->details()->delete();
                $journal->delete();
            }

            // Recreate journal with new data
            $this->recordPayment($payment);
        });
    }
    
    public function deletePaymentJournal(Payment $payment)
    {
        DB::transaction(function () use ($payment) {
            $journal = Journal::where('nomor_referensi', $payment->nomor_pembayaran)
                ->orWhere('nomor_referensi', "PAY-{$payment->id}")
                ->first();

            if ($journal) {
                $journal->details()->delete();
                $journal->delete();
            }
        });
    }

    private function findAccount($officeId, $code)
    {
        return COA::where('office_id', $officeId)
            ->where('kode_akun', $code)
            ->first();
    }
}
