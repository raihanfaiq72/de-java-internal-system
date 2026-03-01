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
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ImportReceiptJob implements ShouldQueue
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
            Log::info("Starting receipt import for: {$this->originalName}");
            
            $fullPath = Storage::path($this->filePath);
            if (!file_exists($fullPath)) {
                throw new \Exception("File not found at path: {$fullPath}");
            }

            $parser = new Parser();
            $pdf = $parser->parseFile($fullPath);
            $text = $pdf->getText();
            
            Log::info("PDF Text extracted: " . substr($text, 0, 200) . "...");

            // Parse Data
            $paymentNo = $this->extractValue($text, '/Payment No\.?\s*:?\s*([A-Z0-9\/-]+)/i');
            $invoiceNo = $this->extractValue($text, '/Invoice No\.?\s*:?\s*([A-Z0-9\/-]+)/i');
            $amountString = $this->extractValue($text, '/Jumlah Rp\s*:?\s*([\d,.]+)/i');
            
            // Clean Amount
            $amount = 0;
            if ($amountString) {
                $cleanAmount = str_replace(['.', ','], ['', '.'], $amountString); // Assuming IDR format 1.000.000,00 -> 1000000.00
                // Wait, usually IDR is 8.140.000,00 or 8,140,000.00? 
                // Screenshot shows: 8,140,000.00 (comma separator, dot decimal) OR (dot separator, comma decimal)?
                // Let's look closely at screenshot: "8,140,000.00". 
                // This looks like standard English format (comma thousand, dot decimal).
                $amount = (float) str_replace(',', '', $amountString);
            }

            // Date Parsing
            // Screenshot: "Jan 7, 2025"
            $dateString = $this->extractValue($text, '/([A-Za-z]{3}\s+\d{1,2},\s+\d{4})/'); 
            $paymentDate = $dateString ? Carbon::parse($dateString)->format('Y-m-d') : date('Y-m-d');

            if (!$invoiceNo) {
                throw new \Exception("Invoice No not found in PDF.");
            }

            // Find Invoice
            $invoice = Invoice::where('nomor_invoice', $invoiceNo)
                ->where('office_id', $this->officeId)
                ->first();

            if (!$invoice) {
                // Try fuzzy match or stripped match?
                // Sometimes PDF has spaces.
                throw new \Exception("Invoice {$invoiceNo} not found in database.");
            }

            // Check if payment already exists
            $exists = Payment::where('nomor_pembayaran', $paymentNo)
                ->where('office_id', $this->officeId)
                ->exists();

            if ($exists) {
                Log::warning("Payment {$paymentNo} already exists. Skipping.");
                // We consider this a success (idempotent) or a skip.
                // Let's just return.
                $this->notifyUser('Import Receipt Skipped', "Payment {$paymentNo} already exists.", 'warning');
                return;
            }

            // Create Payment
            Payment::create([
                'office_id' => $this->officeId,
                'invoice_id' => $invoice->id,
                'nomor_pembayaran' => $paymentNo ?? 'PAY-' . time(), // Fallback
                'ref_no' => $this->originalName,
                'tgl_pembayaran' => $paymentDate,
                'metode_pembayaran' => 'Transfer', // Default or parse if available
                'jumlah_bayar' => $amount,
                'akun_keuangan_id' => 1, // Default Bank/Cash account. Needs logic?
                'catatan' => "Imported from PDF: {$this->originalName}",
            ]);

            // Update Invoice Status
            // Logic: If total paid >= total invoice -> Paid.
            // Check existing payments
            $totalPaid = Payment::where('invoice_id', $invoice->id)->sum('jumlah_bayar');
            
            // We just added one, but the query might include it or not depending on transaction.
            // Since we are in same job, create is synchronous.
            // So totalPaid includes current amount.
            
            if ($totalPaid >= $invoice->total_akhir) {
                $invoice->status_pembayaran = 'Paid';
            } elseif ($totalPaid > 0) {
                $invoice->status_pembayaran = 'Partially Paid';
            }
            $invoice->save();

            $this->notifyUser('Import Receipt Sukses', "Receipt {$paymentNo} untuk Invoice {$invoiceNo} berhasil diimport.", 'success');
            
        } catch (\Throwable $e) {
            Log::error("Receipt Import Failed: " . $e->getMessage());
            $this->notifyUser('Import Receipt Gagal', "Gagal import {$this->originalName}: " . $e->getMessage(), 'error');
            throw $e;
        } finally {
            // Cleanup
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
            $user->notify(new SystemNotification($title, $message, route('sales'), $type));
        }
    }
}
