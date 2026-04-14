<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InvoiceItem;
use App\Models\Invoice;
use App\Services\StockService;
use App\Services\JournalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InvoiceItemController extends Controller
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
        $query = InvoiceItem::with(['product', 'taxes.tax'])
            ->whereHas('invoice', function ($q) {
                $q->where('office_id', session('active_office_id'));
            });

        if ($request->invoice_id) {
            $query->where('invoice_id', $request->invoice_id);
        }

        $data = $query->latest()->paginate($request->get('per_page', 10))->withQueryString();

        return apiResponse(true, 'Data item invoice', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice_id' => 'required|exists:invoices,id',
            'qty' => 'required|numeric|min:0.01',
            'harga_satuan' => 'required|numeric|min:0',
            'tempo' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        if (! session()->has('active_office_id')) {
            return apiResponse(false, 'Silakan pilih outlet terlebih dahulu.', null, null, 422);
        }

        // Validate Invoice ownership
        $invoice = \App\Models\Invoice::where('id', $request->invoice_id)
            ->where('office_id', session('active_office_id'))
            ->first();

        if (! $invoice) {
            return apiResponse(false, 'Invoice tidak valid untuk outlet ini', null, null, 422);
        }

        return DB::transaction(function () use ($request, $invoice) {
            $item = InvoiceItem::create($request->all());

            // 1. Record Stock
            if ($item->product && $item->product->track_stock) {
                if ($invoice->tipe_invoice === 'Purchase') {
                    $this->stockService->recordIn(
                        $item->produk_id,
                        $item->qty,
                        $item->harga_satuan,
                        $invoice->stock_location_id,
                        'Purchase',
                        $invoice->id,
                        "Purchase Invoice #{$invoice->nomor_invoice} (Item Added)"
                    );
                } elseif ($invoice->tipe_invoice === 'Sales') {
                    $this->stockService->recordOut(
                        $item->produk_id,
                        $item->qty,
                        $invoice->stock_location_id,
                        'Sales',
                        $invoice->id,
                        "Sales Invoice #{$invoice->nomor_invoice} (Item Added)"
                    );
                }
            }

            // 2. Refresh Journal
            $this->journalService->deleteInvoiceJournal($invoice);
            if ($invoice->tipe_invoice === 'Purchase') {
                $this->journalService->recordPurchaseInvoice($invoice->fresh(['items.product']));
            } elseif ($invoice->tipe_invoice === 'Sales') {
                $needsApproval = \App\Models\InvoiceApprovalDetail::where('invoice_id', $invoice->id)->where('status', 'pending')->exists();
                if (!$needsApproval) {
                    $this->journalService->recordSalesInvoice($invoice->fresh(['items.product']));
                }
            }

            return apiResponse(true, 'Item invoice ditambahkan', $item, null, 201);
        });
    }

    public function show($id)
    {
        $item = InvoiceItem::with(['product', 'taxes.tax'])
            ->whereHas('invoice', function ($q) {
                $q->where('office_id', session('active_office_id'));
            })
            ->find($id);

        if (! $item) {
            return apiResponse(false, 'Item invoice tidak ditemukan', null, null, 404);
        }

        return apiResponse(true, 'Detail item invoice', $item);
    }

    public function update(Request $request, $id)
    {
        $item = InvoiceItem::whereHas('invoice', function ($q) {
            $q->where('office_id', session('active_office_id'));
        })
            ->find($id);

        if (! $item) {
            return apiResponse(false, 'Item invoice tidak ditemukan', null, null, 404);
        }

        return DB::transaction(function () use ($request, $item) {
            $invoice = $item->invoice;

            // 1. Reverse Old Stock
            if ($item->product && $item->product->track_stock) {
                if ($invoice->tipe_invoice === 'Sales') {
                    $this->stockService->recordIn(
                        $item->produk_id,
                        $item->qty,
                        $item->product->harga_beli,
                        $invoice->stock_location_id,
                        'Sales Return (Edited Item)',
                        $invoice->id,
                        "Stock Return (Invoice Item Edited): #{$invoice->nomor_invoice}"
                    );
                } elseif ($invoice->tipe_invoice === 'Purchase') {
                    $this->stockService->recordOut(
                        $item->produk_id,
                        $item->qty,
                        $invoice->stock_location_id,
                        'Purchase Cancel (Edited Item)',
                        $invoice->id,
                        "Stock Deduct (Purchase Item Edited): #{$invoice->nomor_invoice}"
                    );
                }
            }

            $item->update($request->all());
            $item = $item->fresh(['product']);

            // 2. Record New Stock
            if ($item->product && $item->product->track_stock) {
                if ($invoice->tipe_invoice === 'Purchase') {
                    $this->stockService->recordIn(
                        $item->produk_id,
                        $item->qty,
                        $item->harga_satuan,
                        $invoice->stock_location_id,
                        'Purchase',
                        $invoice->id,
                        "Purchase Invoice #{$invoice->nomor_invoice} (Item Updated)"
                    );
                } elseif ($invoice->tipe_invoice === 'Sales') {
                    $this->stockService->recordOut(
                        $item->produk_id,
                        $item->qty,
                        $invoice->stock_location_id,
                        'Sales',
                        $invoice->id,
                        "Sales Invoice #{$invoice->nomor_invoice} (Item Updated)"
                    );
                }
            }

            // 3. Refresh Journal
            $this->journalService->deleteInvoiceJournal($invoice);
            if ($invoice->tipe_invoice === 'Purchase') {
                $this->journalService->recordPurchaseInvoice($invoice->fresh(['items.product']));
            } elseif ($invoice->tipe_invoice === 'Sales') {
                $needsApproval = \App\Models\InvoiceApprovalDetail::where('invoice_id', $invoice->id)->where('status', 'pending')->exists();
                if (!$needsApproval) {
                    $this->journalService->recordSalesInvoice($invoice->fresh(['items.product']));
                }
            }

            return apiResponse(true, 'Item invoice diperbarui', $item);
        });
    }

    public function destroy($id)
    {
        $item = InvoiceItem::whereHas('invoice', function ($q) {
            $q->where('office_id', session('active_office_id'));
        })
            ->find($id);

        if (! $item) {
            return apiResponse(false, 'Item invoice tidak ditemukan', null, null, 404);
        }

        return DB::transaction(function () use ($item) {
            $invoice = $item->invoice;

            // 1. Reverse Stock
            if ($item->product && $item->product->track_stock) {
                if ($invoice->tipe_invoice === 'Sales') {
                    $this->stockService->recordIn(
                        $item->produk_id,
                        $item->qty,
                        $item->product->harga_beli,
                        $invoice->stock_location_id,
                        'Sales Return (Deleted Item)',
                        $invoice->id,
                        "Stock Return (Invoice Item Deleted): #{$invoice->nomor_invoice}"
                    );
                } elseif ($invoice->tipe_invoice === 'Purchase') {
                    $this->stockService->recordOut(
                        $item->produk_id,
                        $item->qty,
                        $invoice->stock_location_id,
                        'Purchase Cancel (Deleted Item)',
                        $invoice->id,
                        "Stock Deduct (Purchase Item Deleted): #{$invoice->nomor_invoice}"
                    );
                }
            }

            $item->taxes()->delete();
            $item->delete();

            // 2. Refresh Journal
            $this->journalService->deleteInvoiceJournal($invoice);
            if ($invoice->tipe_invoice === 'Purchase') {
                $this->journalService->recordPurchaseInvoice($invoice->fresh(['items.product']));
            } elseif ($invoice->tipe_invoice === 'Sales') {
                $needsApproval = \App\Models\InvoiceApprovalDetail::where('invoice_id', $invoice->id)->where('status', 'pending')->exists();
                if (!$needsApproval) {
                    $this->journalService->recordSalesInvoice($invoice->fresh(['items.product']));
                }
            }

            return apiResponse(true, 'Item invoice dihapus');
        });
    }
}
