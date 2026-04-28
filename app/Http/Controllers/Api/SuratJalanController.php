<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceApprovalDetail;
use App\Models\InvoiceItem;
use App\Models\Journal;
use App\Models\Partner;
use App\Models\User;
use App\Services\JournalService;
use App\Services\StockService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SuratJalanController extends Controller
{
    protected $stockService;

    protected $journalService;

    public function __construct(StockService $stockService, JournalService $journalService)
    {
        $this->stockService = $stockService;
        $this->journalService = $journalService;
    }

    private $views = 'SuratJalan.';

    // ─── Web View ────────────────────────────────────────────────────────────

    public function index()
    {
        $officeId = session('active_office_id');

        $users = collect();
        if ($officeId) {
            $users = User::where('is_sales', true)
                ->whereHas('plots', function ($query) use ($officeId) {
                    $query->where('office_id', $officeId);
                })
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
        }

        return view($this->views.'index', compact('users'));
    }

    // ─── API: List ────────────────────────────────────────────────────────────

    public function apiIndex(Request $request)
    {
        $query = Invoice::with(['mitra', 'items', 'sales'])
            ->where('office_id', session('active_office_id'))
            ->where('tipe_invoice', 'SuratJalan');

        if ($request->tab_status === 'trash') {
            $query->onlyTrashed();
        }

        if ($request->mitra_id) {
            $query->where('mitra_id', $request->mitra_id);
        }

        if ($request->sales_id) {
            $query->where('sales_id', $request->sales_id);
        }

        if ($request->tgl_invoice) {
            $query->whereDate('tgl_invoice', $request->tgl_invoice);
        }

        if ($request->status_dok) {
            $query->where('status_dok', $request->status_dok);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nomor_invoice', 'LIKE', "%{$request->search}%")
                    ->orWhere('ref_no', 'LIKE', "%{$request->search}%")
                    ->orWhereHas('mitra', function ($qMitra) use ($request) {
                        $qMitra->where('nama', 'LIKE', "%{$request->search}%");
                    });
            });
        }

        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sort_by', 'tgl_invoice');
        $sortDir = $request->input('sort_dir', 'desc');

        $query->orderBy($sortBy, $sortDir);

        $data = $query->paginate($perPage)->withQueryString();

        return apiResponse(true, 'Data surat jalan', $data);
    }

    // ─── API: Show ────────────────────────────────────────────────────────────

    public function apiShow($id)
    {
        $data = Invoice::with([
            'mitra',
            'items.product',
            'items.product.supplier',
            'items.product.brand',
            'items.product.category',
            'activities.user',
            'sales',
        ])
            ->where('office_id', session('active_office_id'))
            ->where('tipe_invoice', 'SuratJalan')
            ->find($id);

        if (! $data) {
            return apiResponse(false, 'Surat jalan tidak ditemukan', null, null, 404);
        }

        return apiResponse(true, 'Detail surat jalan', $data);
    }

    // ─── API: Create ──────────────────────────────────────────────────────────

    public function createFullSuratJalan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice.nomor_invoice' => 'required|unique:invoices,nomor_invoice',
            'invoice.tgl_invoice' => 'required|date',
            'invoice.mitra_id' => 'required|exists:mitras,id',
            'invoice.sales_id' => 'nullable|integer|min:0',
            'items' => 'required|array|min:1',
            'items.*.qty' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        if (! session()->has('active_office_id')) {
            return apiResponse(false, 'Silakan pilih outlet terlebih dahulu.', null, null, 422);
        }

        // Validate Mitra ownership & type
        $mitraId = $request->input('invoice.mitra_id');
        $mitra = Partner::where('id', $mitraId)
            ->where('office_id', session('active_office_id'))
            ->first();

        if (! $mitra) {
            return apiResponse(false, 'Mitra tidak valid untuk outlet ini', null, null, 422);
        }

        // Validate sales_id
        $salesId = $request->input('invoice.sales_id');
        if ($salesId && $salesId != 0) {
            if (! User::find($salesId)) {
                return apiResponse(false, 'Sales person tidak valid', null, null, 422);
            }
        }

        return DB::transaction(function () use ($request) {
            $invoiceData = $request->invoice;
            $invoiceData['office_id'] = session('active_office_id');
            $invoiceData['tipe_invoice'] = 'SuratJalan';
            $invoiceData['status_dok'] = 'Draft';
            $invoiceData['status_pembayaran'] = 'Draft';

            // Sanitize sales_id: convert 0 or "0" to null
            if (isset($invoiceData['sales_id']) && (string)$invoiceData['sales_id'] === '0') {
                $invoiceData['sales_id'] = null;
            }

            // Zero out all financial fields – surat jalan has no pricing yet
            $invoiceData['subtotal'] = 0;
            $invoiceData['total_diskon_item'] = 0;
            $invoiceData['diskon_tambahan_nilai'] = 0;
            $invoiceData['biaya_kirim'] = 0;
            $invoiceData['uang_muka'] = 0;
            $invoiceData['total_akhir'] = 0;

            $invoice = Invoice::create($invoiceData);

            foreach ($request->items as $itemData) {
                $itemData['invoice_id'] = $invoice->id;
                $itemData['harga_satuan'] = 0;
                $itemData['diskon_nilai'] = 0;
                $itemData['total_harga_item'] = 0;
                InvoiceItem::create($itemData);
            }

            return apiResponse(true, 'Surat jalan berhasil dibuat', $invoice->load('items'), null, 201);
        });
    }

    // ─── API: Update ──────────────────────────────────────────────────────────

    public function apiUpdate(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $invoice = Invoice::where('office_id', session('active_office_id'))
                ->where('tipe_invoice', 'SuratJalan')
                ->find($id);

            if (! $invoice) {
                return apiResponse(false, 'Surat jalan tidak ditemukan', null, null, 404);
            }

            if ($invoice->status_dok !== 'Draft') {
                return apiResponse(false, 'Hanya surat jalan berstatus Draft yang dapat diubah.', null, null, 422);
            }

            $dataToUpdate = $request->invoice ?? $request->all();
            
            // Sanitize sales_id: convert 0 or "0" to null
            if (isset($dataToUpdate['sales_id']) && (string)$dataToUpdate['sales_id'] === '0') {
                $dataToUpdate['sales_id'] = null;
            }

            // Keep tipe_invoice locked
            unset($dataToUpdate['tipe_invoice']);
            // Keep financial fields zeroed
            $dataToUpdate['subtotal'] = 0;
            $dataToUpdate['total_diskon_item'] = 0;
            $dataToUpdate['diskon_tambahan_nilai'] = 0;
            $dataToUpdate['biaya_kirim'] = 0;
            $dataToUpdate['uang_muka'] = 0;
            $dataToUpdate['total_akhir'] = 0;

            $invoice->update($dataToUpdate);

            if ($request->has('items') && is_array($request->items)) {
                $invoice->items()->each(fn ($item) => $item->delete());

                foreach ($request->items as $itemData) {
                    $itemData['invoice_id'] = $invoice->id;
                    $itemData['harga_satuan'] = 0;
                    $itemData['diskon_nilai'] = 0;
                    $itemData['total_harga_item'] = 0;
                    InvoiceItem::create($itemData);
                }
            }

            return apiResponse(true, 'Surat jalan berhasil diperbarui', $invoice->load('items'));
        });
    }

    // ─── API: Delete (soft) ───────────────────────────────────────────────────

    public function apiDestroy($id)
    {
        $invoice = Invoice::where('office_id', session('active_office_id'))
            ->where('tipe_invoice', 'SuratJalan')
            ->find($id);

        if (! $invoice) {
            return apiResponse(false, 'Surat jalan tidak ditemukan', null, null, 404);
        }

        if ($invoice->status_dok !== 'Draft') {
            return apiResponse(false, 'Hanya surat jalan berstatus Draft yang dapat dihapus.', null, null, 422);
        }

        $invoice->delete();

        return apiResponse(true, 'Surat jalan berhasil dihapus');
    }

    // ─── API: Restore ─────────────────────────────────────────────────────────

    public function apiRestore($id)
    {
        $invoice = Invoice::onlyTrashed()
            ->where('office_id', session('active_office_id'))
            ->where('tipe_invoice', 'SuratJalan')
            ->find($id);

        if (! $invoice) {
            return apiResponse(false, 'Surat jalan tidak ditemukan', null, null, 404);
        }

        $invoice->restore();

        return apiResponse(true, 'Surat jalan berhasil dipulihkan', $invoice);
    }

    // ─── API: Force Delete ────────────────────────────────────────────────────

    public function apiForceDestroy($id)
    {
        $invoice = Invoice::onlyTrashed()
            ->where('office_id', session('active_office_id'))
            ->where('tipe_invoice', 'SuratJalan')
            ->find($id);

        if (! $invoice) {
            return apiResponse(false, 'Surat jalan tidak ditemukan', null, null, 404);
        }

        $invoice->items()->forceDelete();
        $invoice->forceDelete();

        return apiResponse(true, 'Surat jalan berhasil dihapus permanen');
    }

    // ─── API: Convert to Invoice ──────────────────────────────────────────────

    /**
     * Convert a Surat Jalan into a proper Sales Invoice.
     * The caller must supply pricing for each item.
     */
    public function convertToInvoice(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nomor_invoice' => 'required|unique:invoices,nomor_invoice',
            'tgl_invoice' => 'required|date',
            'tgl_jatuh_tempo' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:invoice_items,id',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'items.*.qty' => 'required|numeric|min:0.01',
            'stock_location_id' => 'nullable|exists:stock_locations,id',
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        $suratJalan = Invoice::with('items')
            ->where('office_id', session('active_office_id'))
            ->where('tipe_invoice', 'SuratJalan')
            ->find($id);

        if (! $suratJalan) {
            return apiResponse(false, 'Surat jalan tidak ditemukan', null, null, 404);
        }

        return DB::transaction(function () use ($request, $suratJalan) {
            // Build new invoice data from surat jalan
            $subtotal = 0;
            $totalDiskon = 0;

            $itemsData = collect($request->items)->keyBy('id');

            foreach ($suratJalan->items as $item) {
                $override = $itemsData->get($item->id);
                if (! $override) {
                    continue;
                }

                $harga = (float) $override['harga_satuan'];
                $qty = (float) $override['qty'];
                $diskon = (float) ($override['diskon_nilai'] ?? 0);
                $diskonTipe = $override['diskon_tipe'] ?? 'Fixed';

                $diskonNilai = $diskonTipe === 'Percentage'
                    ? ($harga * $qty * $diskon / 100)
                    : $diskon;

                $total = ($harga * $qty) - $diskonNilai;

                $subtotal += $harga * $qty;
                $totalDiskon += $diskonNilai;
            }

            $diskonTambahan = (float) ($request->diskon_tambahan_nilai ?? 0);
            $diskonTambahanTipe = $request->diskon_tambahan_tipe ?? 'Fixed';
            $diskonTambahanNilai = $diskonTambahanTipe === 'Percentage'
                ? (($subtotal - $totalDiskon) * $diskonTambahan / 100)
                : $diskonTambahan;

            $biayaKirim = (float) ($request->biaya_kirim ?? 0);
            $uangMuka = (float) ($request->uang_muka ?? 0);
            $totalAkhir = ($subtotal - $totalDiskon - $diskonTambahanNilai) + $biayaKirim - $uangMuka;

            // AR Aging Check
            $needsApproval = false;
            $maxOverdue = 0;
            $mitra = Partner::find($suratJalan->mitra_id);

            if ($mitra) {
                $maxOverdue = $this->getMaxOverdueDays($mitra->id);
                if ($maxOverdue > 60) {
                    $needsApproval = true;
                }
            }

            $statusDok = $needsApproval ? 'Draft' : 'Approved';
            $statusPembayaran = $needsApproval ? 'Draft' : 'Unpaid';
            $keterangan = $request->keterangan ?? $suratJalan->keterangan;

            if ($needsApproval) {
                $keterangan .= "\nAuto-Archived: Umur nota > 60 hari ($maxOverdue hari). Perlu ACC Owner.";
            }

            // Create the new Sales invoice
            $newInvoice = Invoice::create([
                'office_id' => $suratJalan->office_id,
                'tipe_invoice' => 'Sales',
                'nomor_invoice' => $request->nomor_invoice,
                'tgl_invoice' => $request->tgl_invoice,
                'tgl_jatuh_tempo' => $request->tgl_jatuh_tempo,
                'ref_no' => $request->ref_no ?? $suratJalan->nomor_invoice,
                'mitra_id' => $suratJalan->mitra_id,
                'sales_id' => $suratJalan->sales_id,
                'status_dok' => $statusDok,
                'status_pembayaran' => $statusPembayaran,
                'subtotal' => $subtotal,
                'total_diskon_item' => $totalDiskon,
                'diskon_tambahan_nilai' => $diskonTambahanNilai,
                'diskon_tambahan_tipe' => $diskonTambahanTipe,
                'biaya_kirim' => $biayaKirim,
                'uang_muka' => $uangMuka,
                'total_akhir' => $totalAkhir,
                'keterangan' => $keterangan,
                'syarat_ketentuan' => $request->syarat_ketentuan ?? $suratJalan->syarat_ketentuan,
            ]);

            if ($needsApproval) {
                InvoiceApprovalDetail::create([
                    'invoice_id' => $newInvoice->id,
                    'office_id' => $suratJalan->office_id,
                    'requested_by' => auth()->id(),
                    'status' => 'pending',
                ]);
            }

            // Copy items with pricing
            foreach ($suratJalan->items as $item) {
                $override = $itemsData->get($item->id);
                if (! $override) {
                    continue;
                }

                $harga = (float) $override['harga_satuan'];
                $qty = (float) $override['qty'];
                $diskon = (float) ($override['diskon_nilai'] ?? 0);
                $diskonTipe = $override['diskon_tipe'] ?? 'Fixed';

                $diskonNilai = $diskonTipe === 'Percentage'
                    ? ($harga * $qty * $diskon / 100)
                    : $diskon;

                $total = ($harga * $qty) - $diskonNilai;

                $newInvoiceItem = InvoiceItem::create([
                    'invoice_id' => $newInvoice->id,
                    'produk_id' => $item->produk_id,
                    'nama_produk_manual' => $item->nama_produk_manual,
                    'deskripsi_produk' => $item->deskripsi_produk,
                    'qty' => $qty,
                    'harga_satuan' => $harga,
                    'diskon_nilai' => $diskonNilai,
                    'diskon_tipe' => $diskonTipe,
                    'total_harga_item' => $total,
                ]);

                // FIFO Stock Tracking
                if (! $needsApproval && $newInvoiceItem->product && $newInvoiceItem->product->track_stock) {
                    $this->stockService->recordOut(
                        $newInvoiceItem->produk_id,
                        $newInvoiceItem->qty,
                        $request->stock_location_id,
                        'Sales',
                        $newInvoice->id,
                        "Sales Invoice #{$newInvoice->nomor_invoice} (from SJ #{$suratJalan->nomor_invoice})"
                    );
                }
            }

            // Automatic Journal Entry
            if (! $needsApproval) {
                $this->journalService->recordSalesInvoice($newInvoice->fresh(['items.product']));
            }

            // Mark surat jalan as converted (status_dok = Sent means "converted")
            $suratJalan->update([
                'status_dok' => 'Sent',
                'ref_no' => $newInvoice->nomor_invoice,
            ]);

            $msg = 'Surat jalan berhasil dikonversi ke invoice penjualan';
            if ($needsApproval) {
                $msg .= '. Harus ACC Owner, dikarenakan umur nota (> 60 hari). Invoice masuk ke Arsip.';
            }

            return apiResponse(true, $msg, [
                'surat_jalan' => $suratJalan,
                'invoice' => $newInvoice->load('items'),
            ], null, 201);
        });
    }

    /**
     * Copy of getMaxOverdueDays from InvoiceController
     */
    private function getMaxOverdueDays($partnerId)
    {
        $maxDays = Invoice::where('mitra_id', $partnerId)
            ->where('tipe_invoice', 'Sales')
            ->where('status_pembayaran', '!=', 'Paid')
            ->withSum(['payment as paid_amount'], 'jumlah_bayar')
            ->get()
            ->filter(fn ($inv) => ($inv->total_akhir - ($inv->paid_amount ?? 0)) > 0)
            ->map(function ($inv) {
                $dueDate = $inv->tgl_jatuh_tempo ? Carbon::parse($inv->tgl_jatuh_tempo) : Carbon::parse($inv->tgl_invoice);

                return $dueDate->isPast() ? $dueDate->diffInDays(Carbon::now()) : 0;
            })
            ->max() ?? 0;

        return $maxDays;
    }
}
