<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceItemTax;
use App\Models\Partner;
use App\Models\Permission;
use App\Models\UserOfficeRole;
use Carbon\Carbon;
use App\Services\JournalService;
use App\Services\StockService;
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
        $query = Invoice::with(['mitra', 'items.taxes', 'payment', 'items.product'])
            ->where('office_id', session('active_office_id'))
            ->withSum('payment', 'jumlah_bayar');

        if ($request->tab_status === 'trash') {
            $query->onlyTrashed();
        } elseif ($request->tab_status === 'archive') {
            $query->where(function ($q) {
                $q->where('status_dok', 'Draft')
                    ->orWhere('tgl_invoice', '<=', now()->subDays(60));
            });
        }

        if ($request->tipe_invoice) {
            $query->where('tipe_invoice', $request->tipe_invoice);
        }

        if ($request->mitra_id) {
            $query->where('mitra_id', $request->mitra_id);
        }

        if ($request->exclude_delivered) {
            $query->whereDoesntHave('deliveryOrderInvoices', function ($q) {
                $q->whereNotIn('delivery_status', ['failed', 'rejected', 'returned']);
            });
        }

        if ($request->status_dok) {
            $query->where('status_dok', $request->status_dok);
        }

        if ($request->status_pembayaran) {
            $query->where('status_pembayaran', $request->status_pembayaran);
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

        $perPage = $request->get('per_page', 10);
        $data = $query->latest()->paginate($perPage);

        return apiResponse(true, 'Data invoice', $data);
    }

    public function show($id)
    {
        $data = Invoice::with(['mitra', 'items.taxes.tax', 'payment', 'items.product', 'items.product.supplier', 'items.product.brand', 'items.product.category'])
            ->where('office_id', session('active_office_id'))
            ->find($id);

        if (! $data) {
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
            'mitra_id' => 'required|exists:mitras,id',
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        if (! session()->has('active_office_id')) {
            return apiResponse(false, 'Silakan pilih outlet terlebih dahulu.', null, null, 422);
        }

        $officeId = session('active_office_id');

        // Validate Partner ownership
        $partner = Partner::where('id', $request->mitra_id)
            ->where('office_id', $officeId)
            ->first();
        if (! $partner) {
            return apiResponse(false, 'Mitra tidak valid untuk outlet ini', null, null, 422);
        }

        $input = $request->all();
        $input['office_id'] = $officeId;

        // AR Aging Check (Only for Sales)
        $warningMessage = '';
        if ($request->tipe_invoice === 'Sales') {
            $maxOverdue = $this->getMaxOverdueDays($partner->id);
            if ($maxOverdue > 60) {
                // Check if user is owner
                if (! $this->isOwner(auth()->id(), $officeId)) {
                    $input['status_dok'] = 'Draft';
                    $input['perlu_acc_admin'] = true;
                    $input['keterangan'] = ($request->keterangan ? $request->keterangan."\n" : '')."Auto-Archived: Umur nota > 60 hari ($maxOverdue hari). Perlu ACC Owner.";
                    
                    $warningMessage = "Harus ACC Owner, dikarenakan umur nota ($maxOverdue hari). Invoice masuk ke Arsip.";
                }
            }
        }

        $invoice = Invoice::create($input);

        $msg = 'Invoice berhasil dibuat';
        if ($warningMessage) {
            $msg .= '. '.$warningMessage;
        }

        return apiResponse(true, $msg, $invoice, null, 201);
    }

    private function getMaxOverdueDays($partnerId)
    {
        $invoices = Invoice::where('mitra_id', $partnerId)
            ->where('tipe_invoice', 'Sales')
            ->where('status_pembayaran', '!=', 'Paid')
            ->whereNull('deleted_at')
            ->with('payment')
            ->get();

        $maxDays = 0;
        foreach ($invoices as $inv) {
            $paid = $inv->payment->sum('jumlah_bayar');
            $remaining = $inv->total_akhir - $paid;

            if ($remaining <= 0) {
                continue;
            }

            // Determine Due Date (Use tgl_invoice if tgl_jatuh_tempo is null)
            $dueDate = $inv->tgl_jatuh_tempo ? Carbon::parse($inv->tgl_jatuh_tempo) : Carbon::parse($inv->tgl_invoice);
            
            if ($dueDate->isPast()) {
                $days = $dueDate->diffInDays(Carbon::now());
                if ($days > $maxDays) {
                    $maxDays = $days;
                }
            }
        }

        return $maxDays;
    }

    private function isOwner($userId, $officeId)
    {
        // Get user's role in this office
        $userRole = UserOfficeRole::with('role')->where('user_id', $userId)
            ->where('office_id', $officeId)
            ->first();

        if (! $userRole || ! $userRole->role) {
            return false;
        }

        // Count permissions
        $rolePermCount = $userRole->role->permissions()->count();
        $totalPermCount = Permission::count();

        return $rolePermCount >= $totalPermCount;
    }

    public function update(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $invoice = Invoice::where('office_id', session('active_office_id'))->find($id);
            if (! $invoice) {
                return apiResponse(false, 'Invoice tidak ditemukan', null, null, 404);
            }

            $dataToUpdate = $request->invoice ?? $request->all();

            // Handle Logo Upload
            if ($request->hasFile('logo_img')) {
                $file = $request->file('logo_img');
                $filename = 'kop_'.time().'_'.$file->getClientOriginalName();
                $path = $file->move(public_path('invoices/kop'), $filename);
                $dataToUpdate['logo_img'] = 'invoices/kop/'.$filename;
            }

            // Update Invoice Details
            $invoice->update($dataToUpdate);

            // If items are provided, replace them (Full Sync)
            if ($request->has('items') && is_array($request->items)) {

                // Get old items IDs for logging/cleanup if needed, then delete
                $invoice->items()->each(function ($item) {
                    $item->taxes()->delete();
                    $item->delete();
                });

                foreach ($request->items as $itemData) {
                    $itemData['invoice_id'] = $invoice->id;
                    $item = InvoiceItem::create($itemData);

                    if (! empty($itemData['taxes'])) {
                        foreach ($itemData['taxes'] as $taxData) {
                            InvoiceItemTax::create([
                                'invoice_item_id' => $item->id,
                                'tax_id' => $taxData['tax_id'],
                                'nilai_pajak_diterapkan' => $taxData['nilai_pajak_diterapkan'] ?? 0,
                            ]);
                        }
                    }
                }
            }

            return apiResponse(true, 'Invoice berhasil diperbarui', $invoice->load('items.taxes.tax'));
        });
    }

    public function destroy($id)
    {
        $invoice = Invoice::where('office_id', session('active_office_id'))->find($id);
        if (! $invoice) {
            return apiResponse(false, 'Invoice tidak ditemukan', null, null, 404);
        }

        $before = $invoice->toArray();
        $invoice->delete();

        return apiResponse(true, 'Invoice berhasil dihapus');
    }

    public function search(Request $request, $value)
    {
        $query = Invoice::with(['mitra', 'items.product', 'items.taxes', 'payment'])
            ->withSum('payment', 'jumlah_bayar')
            ->where('office_id', session('active_office_id'))
            ->where(function ($q) use ($value) {
                $q->where('nomor_invoice', 'LIKE', "%$value%")
                    ->orWhere('ref_no', 'LIKE', "%$value%")
                    ->orWhereHas('mitra', function ($qMitra) use ($value) {
                        $qMitra->where('nama', 'LIKE', "%{$value}%");
                    });
            });

        if ($request->tipe_invoice) {
            $query->where('tipe_invoice', $request->tipe_invoice);
        }
        
        // Filter: Exclude invoices already in delivery orders (unless status is failed/rejected)
        if ($request->exclude_delivered) {
            $query->whereDoesntHave('deliveryOrderInvoices', function ($q) {
                $q->whereNotIn('delivery_status', ['failed', 'rejected', 'returned']);
            });
        }

        $data = $query->latest()->paginate(10);

        return apiResponse(true, 'Hasil pencarian invoice', $data);
    }

    public function createFullInvoice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice.tipe_invoice' => 'required|in:Sales,Purchase',
            'invoice.nomor_invoice' => 'required|unique:invoices,nomor_invoice',
            'invoice.tgl_invoice' => 'required|date',
            'invoice.mitra_id' => 'required|exists:mitras,id',
            'invoice.sales_id' => 'nullable|integer|min:0',

            'items' => 'required|array|min:1',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.tempo' => 'nullable|integer|min:0',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'items.*.taxes' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        if (! session()->has('active_office_id')) {
            return apiResponse(false, 'Silakan pilih outlet terlebih dahulu.', null, null, 422);
        }

        // Validate Mitra ownership
        $mitraId = $request->input('invoice.mitra_id');
        $mitra = Partner::where('id', $mitraId)
            ->where('office_id', session('active_office_id'))
            ->first();
        if (! $mitra) {
            return apiResponse(false, 'Mitra tidak valid untuk outlet ini', null, null, 422);
        }

        // Validate sales_id jika bukan 0
        $salesId = $request->input('invoice.sales_id');
        if ($salesId && $salesId != 0) {
            $salesUser = \App\Models\User::find($salesId);
            if (! $salesUser) {
                return apiResponse(false, 'Sales person tidak valid', null, null, 422);
            }
        }

        // AR Aging Check (Only for Sales)
        $warningMessage = '';
        $invoiceData = $request->invoice;
        if (($invoiceData['tipe_invoice'] ?? '') === 'Sales') {
            $maxOverdue = $this->getMaxOverdueDays($mitra->id);
            if ($maxOverdue > 60) {
                // Check if user is owner
                if (! $this->isOwner(auth()->id(), session('active_office_id'))) {
                    $invoiceData['status_dok'] = 'Draft';
                    $invoiceData['perlu_acc_admin'] = true;
                    $invoiceData['keterangan'] = ($invoiceData['keterangan'] ?? '')."\nAuto-Archived: Umur nota > 60 hari ($maxOverdue hari). Perlu ACC Owner.";

                    $warningMessage = "Harus ACC Owner, dikarenakan umur nota ($maxOverdue hari). Invoice masuk ke Arsip.";
                }
            }
        }

        // Validate Stock Location if provided
        if (! empty($request->invoice['stock_location_id'])) {
            $location = \App\Models\StockLocation::where('id', $request->invoice['stock_location_id'])
                ->where('office_id', session('active_office_id'))
                ->first();

            if (! $location) {
                return apiResponse(false, 'Lokasi stok tidak valid untuk outlet ini', null, null, 422);
            }
        }

        return DB::transaction(function () use ($request, $invoiceData, $warningMessage) {

            // $invoiceData = $request->invoice; // Removed because defined above
            $invoiceData['office_id'] = session('active_office_id');
            $invoice = Invoice::create($invoiceData);

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

                // Tax Logic (Simplified)
                if (! empty($itemData['taxes'])) {
                    foreach ($itemData['taxes'] as $taxData) {
                        if (! empty($taxData['tax_id'])) {
                            $tax = InvoiceItemTax::create([
                                'invoice_item_id' => $item->id,
                                'tax_id' => $taxData['tax_id'],
                                'nilai_pajak_diterapkan' => $taxData['nilai_pajak_diterapkan'] ?? 0,
                            ]);
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

            $msg = 'Invoice berhasil dibuat';
            if ($warningMessage) {
                $msg .= '. '.$warningMessage;
            }

            return apiResponse(true, $msg, $invoice->load('items'), null, 201);
        });
    }

    public function accAdmin(Request $request) {
        $query = Invoice::with(['mitra', 'items'])
            ->where('status_dok', 'Draft')
            ->where('perlu_acc_admin', true)
            ->where('office_id', session('active_office_id'));

        if ($request->tipe_invoice) {
            $query->where('tipe_invoice', $request->tipe_invoice);
        }

        $invoice = $query->latest()->get();

        return apiResponse(true, 'Invoice berhasil diambil', $invoice, null, 200);
    }
    
}
