<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Smalot\PdfParser\Parser;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\User;
use App\Services\JournalService;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ImportPurchaseReceiptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $originalName;
    protected $officeId;
    protected $userId;

    public function __construct($filePath, $originalName, $officeId, $userId)
    {
        $this->filePath = $filePath;
        $this->originalName = $originalName;
        $this->officeId = $officeId;
        $this->userId = $userId;
    }

    public function handle()
    {
        try {
            Log::info("Starting purchase receipt import for: {$this->originalName}");
            
            $fullPath = Storage::path($this->filePath);
            if (!file_exists($fullPath)) {
                throw new \Exception("File not found at path: {$fullPath}");
            }

            $parser = new Parser();
            $pdf = $parser->parseFile($fullPath);
            $text = $pdf->getText();
            
            Log::info("PDF Text extracted: " . substr($text, 0, 200) . "...");

            // Parse Data
            // Assuming format is similar to Sales Receipt
            $paymentNo = $this->extractValue($text, '/Payment No\.?\s*:?\s*([A-Z0-9\/-]+)/i');
            $invoiceNo = $this->extractValue($text, '/Invoice No\.?\s*:?\s*([A-Z0-9\/-]+)/i');
            $amountString = $this->extractValue($text, '/Jumlah Rp\s*:?\s*([\d,.]+)/i');
            
            // Clean Amount
            $amount = 0;
            if ($amountString) {
                // Assuming IDR format 1.000.000,00 -> 1000000.00
                $amount = (float) str_replace(',', '', $amountString);
            }

            // Date Parsing
            $dateString = $this->extractValue($text, '/([A-Za-z]{3}\s+\d{1,2},\s+\d{4})/'); 
            $paymentDate = $dateString ? Carbon::parse($dateString)->format('Y-m-d') : date('Y-m-d');

            if (!$invoiceNo) {
                throw new \Exception("Invoice No not found in PDF.");
            }

            // Find Invoice (Purchase)
            $invoice = Invoice::where('nomor_invoice', $invoiceNo)
                ->where('office_id', $this->officeId)
                ->where('tipe_invoice', 'Purchase') // Ensure it is Purchase Invoice
                ->first();

            if (!$invoice) {
                throw new \Exception("Purchase Invoice {$invoiceNo} not found in database.");
            }

            // Check if payment already exists
            $exists = Payment::where('nomor_pembayaran', $paymentNo)
                ->where('office_id', $this->officeId)
                ->exists();

            if ($exists) {
                Log::warning("Payment {$paymentNo} already exists. Skipping.");
                $this->notifyUser('Import Purchase Receipt Skipped', "Payment {$paymentNo} already exists.", 'warning');
                return;
            }

            // Create Payment
            $payment = Payment::create([
                'office_id' => $this->officeId,
                'invoice_id' => $invoice->id,
                'nomor_pembayaran' => $paymentNo ?? 'PAY-' . time(),
                'ref_no' => $this->originalName,
                'tgl_pembayaran' => $paymentDate,
                'metode_pembayaran' => 'Transfer',
                'jumlah_bayar' => $amount,
                'akun_keuangan_id' => 1,
                'catatan' => "Imported from PDF: {$this->originalName}",
            ]);

            // record journal
            $journalService = app(JournalService::class);
            $journalService->recordPayment($payment);

            // Update Invoice Status
            $totalPaid = Payment::where('invoice_id', $invoice->id)->sum('jumlah_bayar');
            
            if ($totalPaid >= $invoice->total_akhir) {
                $invoice->status_pembayaran = 'Paid';
            } elseif ($totalPaid > 0) {
                $invoice->status_pembayaran = 'Partially Paid';
            }
            $invoice->save();

            $this->notifyUser('Import Purchase Receipt Sukses', "Receipt {$paymentNo} untuk Invoice {$invoiceNo} berhasil diimport.", 'success');
            
        } catch (\Throwable $e) {
            Log::error("Purchase Receipt Import Failed: " . $e->getMessage());
            $this->notifyUser('Import Purchase Receipt Gagal', "Gagal import {$this->originalName}: " . $e->getMessage(), 'error');
            throw $e;
        } finally {
            if (Storage::exists($this->filePath)) {
                Storage::delete($this->filePath);
            }
        }
    }

    private function extractValue($text, $regex)
    {
        if (preg_match($regex, $text, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    private function notifyUser($title, $message, $type)
    {
        $user = User::find($this->userId);
        if ($user) {
            $user->notify(new SystemNotification($title, $message, route('purchase'), $type));
        }
    }
}
