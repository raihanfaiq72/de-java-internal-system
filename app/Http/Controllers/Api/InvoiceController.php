<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\ActivityLog;
use App\Models\InvoiceItem;
use App\Models\InvoiceItemTax;
use App\Models\Partner;
use App\Services\StockService;
use App\Services\JournalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
{
    protected $stockService;
    protected $journalService;

    public function __construct(StockService $stockService, JournalService $journalService)
    {
        $this->stockService = $stockService;
        $this->journalService = $journalService;
    }
    public function index(Request $request)
    {
        $query = Invoice::with(['mitra', 'items.product.unit', 'items.taxes', 'payment'])
            ->where('office_id', session('active_office_id'))
            ->withSum('payment', 'jumlah_bayar');

        if ($request->tab_status === 'trash') {
            $query->onlyTrashed();
        } elseif ($request->tab_status === 'archive') {
            $query->where('status_dok', 'Archived');
        }

        if ($request->tipe_invoice) {
            $query->where('tipe_invoice', $request->tipe_invoice);
        }

        if ($request->status_dok) {
            $query->where('status_dok', $request->status_dok);
        }

        if ($request->status_pembayaran) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }
        
        if ($request->search) {
             $query->where(function($q) use ($request) {
                $q->where('nomor_invoice', 'LIKE', "%{$request->search}%")
                  ->orWhere('ref_no', 'LIKE', "%{$request->search}%")
                  ->orWhereHas('mitra', function($qMitra) use ($request) {
                      $qMitra->where('nama', 'LIKE', "%{$request->search}%");
                  });
             });
        }

        $data = $query->latest()->paginate(10);

        return apiResponse(true, 'Data invoice', $data);
    }

    public function show($id)
    {
        $data = Invoice::with(['mitra', 'items.product.unit', 'items.taxes.tax', 'payment'])
            ->where('office_id', session('active_office_id'))
            ->find($id);

        if (!$data) {
            return apiResponse(false, 'Invoice tidak ditemukan', null, null, 404);
        }

        return apiResponse(true, 'Detail invoice', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipe_invoice' => 'required|in:Sales,Purchase',
            'nomor_invoice' => 'required|unique:invoices,nomor_invoice',
            'tgl_invoice' => 'required|date',
            'mitra_id' => 'required|exists:mitras,id'
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        if (!session()->has('active_office_id')) {
            return apiResponse(false, 'Silakan pilih outlet terlebih dahulu.', null, null, 422);
        }

        // Validate Partner ownership
        $partner = Partner::where('id', $request->mitra_id)
            ->where('office_id', session('active_office_id'))
            ->first();
        if (!$partner) {
            return apiResponse(false, 'Mitra tidak valid untuk outlet ini', null, null, 422);
        }

        $input = $request->all();
        $input['office_id'] = session('active_office_id');

        $invoice = Invoice::create($input);

        $this->logActivity('Create', 'invoices', $invoice->id, null, $invoice);

        return apiResponse(true, 'Invoice berhasil dibuat', $invoice, null, 201);
    }

    public function update(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $invoice = Invoice::where('office_id', session('active_office_id'))->find($id);
            if (!$invoice) {
                return apiResponse(false, 'Invoice tidak ditemukan', null, null, 404);
            }

            // Update Invoice Details
            $invoice->update($request->invoice ?? $request->all());

            // If items are provided, replace them (Full Sync)
            if ($request->has('items') && is_array($request->items)) {
                
                // Get old items IDs for logging/cleanup if needed, then delete
                $invoice->items()->each(function($item) {
                    $item->taxes()->delete();
                    $item->delete();
                });

                foreach ($request->items as $itemData) {
                    $itemData['invoice_id'] = $invoice->id;
                    $item = InvoiceItem::create($itemData);

                    if (!empty($itemData['taxes'])) {
                        foreach ($itemData['taxes'] as $taxData) {
                            InvoiceItemTax::create([
                                'invoice_item_id'       => $item->id,
                                'tax_id'                => $taxData['tax_id'],
                                'nilai_pajak_diterapkan'=> $taxData['nilai_pajak_diterapkan'] ?? 0
                            ]);
                        }
                    }
                }
            }

            $this->logActivity('Update', 'invoices', $id, null, $invoice->fresh('items'));

            return apiResponse(true, 'Invoice berhasil diperbarui', $invoice->load('items.taxes.tax'));
        });
    }

    public function destroy($id)
    {
        $invoice = Invoice::where('office_id', session('active_office_id'))->find($id);
        if (!$invoice) {
            return apiResponse(false, 'Invoice tidak ditemukan', null, null, 404);
        }

        $before = $invoice->toArray();
        $invoice->delete();

        $this->logActivity('Soft Delete', 'invoices', $id, $before, null);

        return apiResponse(true, 'Invoice berhasil dihapus');
    }

    public function search(Request $request, $value)
    {
        $query = Invoice::with(['mitra', 'items.product', 'items.taxes', 'payment'])
            ->withSum('payment', 'jumlah_bayar')
            ->where('office_id', session('active_office_id'))
            ->where(function($q) use ($value) {
                $q->where('nomor_invoice', 'LIKE', "%$value%")
                  ->orWhere('ref_no', 'LIKE', "%$value%")
                  ->orWhereHas('mitra', function($qMitra) use ($value) {
                      $qMitra->where('nama', 'LIKE', "%{$value}%");
                  });
            });

        if ($request->tipe_invoice) {
            $query->where('tipe_invoice', $request->tipe_invoice);
        }

        $data = $query->latest()->paginate(10);

        return apiResponse(true, 'Hasil pencarian invoice', $data);
    }

    public function createFullInvoice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice.tipe_invoice'   => 'required|in:Sales,Purchase',
            'invoice.nomor_invoice'  => 'required|unique:invoices,nomor_invoice',
            'invoice.tgl_invoice'    => 'required|date',
            'invoice.mitra_id'       => 'required|exists:mitras,id',

            'items'                  => 'required|array|min:1',
            'items.*.qty'            => 'required|numeric|min:0.01',
            'items.*.harga_satuan'   => 'required|numeric|min:0',
            'items.*.taxes'          => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        if (!session()->has('active_office_id')) {
            return apiResponse(false, 'Silakan pilih outlet terlebih dahulu.', null, null, 422);
        }

        // Validate Mitra ownership
        $mitraId = $request->input('invoice.mitra_id');
        $mitra = Partner::where('id', $mitraId)
            ->where('office_id', session('active_office_id'))
            ->first();
        if (!$mitra) {
            return apiResponse(false, 'Mitra tidak valid untuk outlet ini', null, null, 422);
        }

        // Validate Stock Location if provided
        if (!empty($request->invoice['stock_location_id'])) {
            $location = \App\Models\StockLocation::where('id', $request->invoice['stock_location_id'])
                ->where('office_id', session('active_office_id'))
                ->first();
            
            if (!$location) {
                return apiResponse(false, 'Lokasi stok tidak valid untuk outlet ini', null, null, 422);
            }
        }

        return DB::transaction(function () use ($request) {

            $invoiceData = $request->invoice;
            $invoiceData['office_id'] = session('active_office_id');
            $invoice = Invoice::create($invoiceData);

            $this->logActivity(
                'Create',
                'invoices',
                $invoice->id,
                null,
                $invoice
            );

            foreach ($request->items as $itemData) {

                $itemData['invoice_id'] = $invoice->id;

                $item = InvoiceItem::create($itemData);

                // FIFO Stock Tracking
                if ($item->product && $item->product->track_stock) {
                    if ($invoice->tipe_invoice === 'Purchase') {
                        $this->stockService->recordIn(
                            $item->produk_id, // Fixed: use produk_id from InvoiceItem
                            $item->qty,
                            $item->harga_satuan,
                            $request->invoice['stock_location_id'] ?? null, // Support location
                            'Purchase',
                            $invoice->id,
                            "Purchase Invoice #{$invoice->nomor_invoice}"
                        );
                    } elseif ($invoice->tipe_invoice === 'Sales') {
                        $this->stockService->recordOut(
                            $item->produk_id, // Fixed: use produk_id from InvoiceItem
                            $item->qty,
                            $request->invoice['stock_location_id'] ?? null, // Support location
                            'Sales',
                            $invoice->id,
                            "Sales Invoice #{$invoice->nomor_invoice}"
                        );
                    }
                }

                if (!empty($itemData['taxes'])) {
                    foreach ($itemData['taxes'] as $taxData) {
                        if (!empty($taxData['tax_id'])) {
                            $tax = InvoiceItemTax::create([
                                'invoice_item_id'       => $item->id,
                                'tax_id'                => $taxData['tax_id'],
                                'nilai_pajak_diterapkan'=> $taxData['nilai_pajak_diterapkan'] ?? 0
                            ]);

                            $this->logActivity(
                                'Create',
                                'invoice_item_taxes',
                                $tax->id,
                                null,
                                $tax
                            );
                        }
                    }
                }
            }

            // Automatic Journal Entry
            if ($invoice->tipe_invoice === 'Purchase') {
                $this->journalService->recordPurchaseInvoice($invoice->fresh(['items.product']));
            } elseif ($invoice->tipe_invoice === 'Sales') {
                $this->journalService->recordSalesInvoice($invoice->fresh(['items.product']));
            }

            return apiResponse(true, 'Invoice berhasil dibuat lengkap', [
                'invoice' => $invoice->load('items.taxes.tax')
            ], null, 201);
        });
    }

    private function logActivity($tindakan, $tabel, $dataId, $before, $after)
    {
        ActivityLog::create([
            'office_id' => session('active_office_id'),
            'user_id' => 1,
            'tindakan' => $tindakan,
            'tabel_terkait' => $tabel,
            'data_id' => $dataId,
            'data_sebelum' => $before,
            'data_sesudah' => $after,
            'ip_address' => request()->ip(),
        ]);
    }
}