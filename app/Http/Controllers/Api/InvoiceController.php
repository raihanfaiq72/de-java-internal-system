<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceApprovalDetail;
use App\Models\InvoiceItem;
use App\Models\InvoiceItemTax;
use App\Models\Journal;
use App\Models\Partner;
use App\Models\Permission;
use App\Models\StockLocation;
use App\Models\User;
use App\Models\UserOfficeRole;
use App\Services\JournalService;
use App\Services\StockService;
use Carbon\Carbon;
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
        if ($request->tipe_invoice === 'Sales') {
            Invoice::where('tipe_invoice', 'Sales')
                ->where('office_id', session('active_office_id'))
                ->where('status_dok', 'Approved')
                ->where('status_pembayaran', '!=', 'Paid')
                ->whereDate('tgl_invoice', '<=', Carbon::now()->subDays(60)->toDateString())
                ->update(['status_pembayaran' => 'Overdue']);
        }

        $query = Invoice::with(['mitra', 'items.taxes', 'payment', 'items.product', 'approvals' => function ($q) {
            $q->latest();
        }])
            ->where('office_id', session('active_office_id'))
            ->withSum('payment', 'jumlah_bayar');

        if ($request->tab_status === 'trash') {
            $query->onlyTrashed();
        } elseif ($request->tab_status === 'archive') {
            $query->where('status_dok', 'Draft')
                ->where('status_pembayaran', 'Draft');
        } elseif ($request->tab_status === 'overdue') {
            $query->where('status_pembayaran', 'Overdue');
        } else {
            // Active Tab (Default)
            // Show Drafts that are NOT archived (Draft + non-Draft payment), and all non-Draft invoices
            $query->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->where('status_dok', 'Draft')
                        ->where('status_pembayaran', '!=', 'Draft');
                })->orWhere('status_dok', '!=', 'Draft');
            })->where('status_pembayaran', '!=', 'Overdue');
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
        if ($perPage >= 1000) {
            $data = $query->latest()->get();
        } else {
            $data = $query->latest()->paginate($perPage)->withQueryString();
        }

        return apiResponse(true, 'Data invoice', $data);
    }

    public function show($id)
    {
        $data = Invoice::with([
            'mitra',
            'items.taxes.tax',
            'payment' => function ($q) {
                $q->latest();
            },
            'items.product',
            'items.product.supplier',
            'items.product.brand',
            'items.product.category',
            'approvals.requestedBy',
            'approvals.processedBy',
            'activities.user',
            'sales',
        ])
            ->withSum('payment', 'jumlah_bayar')
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

        // AR Aging Check - Determine if approval is needed
        $needsApproval = false;
        $maxOverdue = 0;

        if ($request->tipe_invoice === 'Sales') {
            $maxOverdue = $this->getMaxOverdueDays($partner->id);

            if ($maxOverdue > 60 && ! $this->isOwner(auth()->id(), $officeId)) {
                $needsApproval = true;
            }
        }

        // Force Draft status if approval is needed
        if ($needsApproval) {
            $input['status_dok'] = 'Draft';
            $input['status_pembayaran'] = 'Draft';
        }

        $invoice = Invoice::create($input);

        // Create approval request if needed
        if ($needsApproval) {
            InvoiceApprovalDetail::create([
                'invoice_id' => $invoice->id,
                'office_id' => $officeId,
                'requested_by' => auth()->id(),
                'status' => 'pending',
            ]);

            $warningMessage = "Harus ACC Owner, dikarenakan umur nota ($maxOverdue hari). Invoice masuk ke Arsip.";
        }

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

            $message = 'Invoice berhasil diperbarui';

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

                // 1. Reverse Stock for old items (Only if already approved)
                if ($invoice->status_dok === 'Approved') {
                    foreach ($invoice->items as $item) {
                        if ($item->product && $item->product->track_stock) {
                            if ($invoice->tipe_invoice === 'Sales') {
                                $this->stockService->recordIn(
                                    $item->produk_id,
                                    $item->qty,
                                    $item->product->harga_beli,
                                    $invoice->stock_location_id,
                                    'Sales Return (Edited)',
                                    $invoice->id,
                                    "Stock Return (Invoice Edited): #{$invoice->nomor_invoice}"
                                );
                            } elseif ($invoice->tipe_invoice === 'Purchase') {
                                $this->stockService->recordOut(
                                    $item->produk_id,
                                    $item->qty,
                                    $invoice->stock_location_id,
                                    'Purchase Cancel (Edited)',
                                    $invoice->id,
                                    "Stock Deduct (Purchase Edited): #{$invoice->nomor_invoice}"
                                );
                            }
                        }
                    }

                    // 2. Reverse COA
                    $this->journalService->deleteInvoiceJournal($invoice);

                    // 3. Delete old items
                    $invoice->items()->each(function ($item) {
                        $item->taxes()->delete();
                        $item->delete();
                    });

                    // 4. Create new items and record new Stock
                    foreach ($request->items as $itemData) {
                        $itemData['invoice_id'] = $invoice->id;
                        $item = InvoiceItem::create($itemData);

                        // FIFO Stock Tracking (Only if already approved)
                        if ($invoice->status_dok === 'Approved' && $item->product && $item->product->track_stock) {
                            if ($invoice->tipe_invoice === 'Purchase') {
                                $this->stockService->recordIn(
                                    $item->produk_id,
                                    $item->qty,
                                    $item->harga_satuan,
                                    $invoice->stock_location_id,
                                    'Purchase',
                                    $invoice->id,
                                    "Purchase Invoice #{$invoice->nomor_invoice} (Edited)"
                                );
                            } elseif ($invoice->tipe_invoice === 'Sales') {
                                $this->stockService->recordOut(
                                    $item->produk_id,
                                    $item->qty,
                                    $invoice->stock_location_id,
                                    'Sales',
                                    $invoice->id,
                                    "Sales Invoice #{$invoice->nomor_invoice} (Edited)"
                                );
                            }
                        }

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

                    // 5. Re-record Journal entries
                    if ($invoice->tipe_invoice === 'Purchase') {
                        $this->journalService->recordPurchaseInvoice($invoice->fresh(['items.product']));
                    } elseif ($invoice->tipe_invoice === 'Sales') {
                        // Only record if not pending approval
                        $needsApproval = InvoiceApprovalDetail::where('invoice_id', $invoice->id)
                            ->where('status', 'pending')
                            ->exists();

                        if (! $needsApproval) {
                            $this->journalService->recordSalesInvoice($invoice->fresh(['items.product']));
                        } else {
                            $message .= '. Journal entry ditangguhkan (perlu ACC).';
                        }
                    }
                }
            }

            return apiResponse(true, $message, $invoice->load('items.taxes.tax'));
        });
    }

    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $invoice = Invoice::where('office_id', session('active_office_id'))->find($id);
            if (! $invoice) {
                return apiResponse(false, 'Invoice tidak ditemukan', null, null, 404);
            }

            // If NOT archived, we MUST archive first to preserve data integrity (Stock/Journals)
            if ($invoice->status_dok !== 'Draft' || $invoice->status_pembayaran !== 'Draft') {
                // Reverse COA
                $this->journalService->deleteInvoiceJournal($invoice);

                // Reverse Stock
                foreach ($invoice->items as $item) {
                    if ($item->product && $item->product->track_stock) {
                        if ($invoice->tipe_invoice === 'Sales') {
                            $this->stockService->recordIn(
                                $item->produk_id,
                                $item->qty,
                                $item->product->harga_beli,
                                $invoice->stock_location_id,
                                'Sales Return (Archived/Deleted)',
                                $invoice->id,
                                "Stock Return (Invoice Deleted): #{$invoice->nomor_invoice}"
                            );
                        } elseif ($invoice->tipe_invoice === 'Purchase') {
                            $this->stockService->recordOut(
                                $item->produk_id,
                                $item->qty,
                                $invoice->stock_location_id,
                                'Purchase Cancel (Archived/Deleted)',
                                $invoice->id,
                                "Stock Deduct (Purchase Invoice Deleted): #{$invoice->nomor_invoice}"
                            );
                        }
                    }
                }

                // Update Status to Archived before deleting
                $invoice->update([
                    'status_dok' => 'Draft',
                    'status_pembayaran' => 'Draft',
                ]);
            }

            $invoice->delete();

            return apiResponse(true, 'Invoice berhasil dihapus (Archive)');
        });
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

        $perPage = $request->get('per_page', 10);
        if ($perPage >= 1000) {
            $data = $query->latest()->get();
        } else {
            $data = $query->latest()->paginate($perPage)->withQueryString();
        }

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
            $salesUser = User::find($salesId);
            if (! $salesUser) {
                return apiResponse(false, 'Sales person tidak valid', null, null, 422);
            }
        }

        // AR Aging Check (Only for Sales)
        $warningMessage = '';
        $invoiceData = $request->invoice;
        $needsApproval = false;
        $maxOverdue = 0;

        if (($invoiceData['tipe_invoice'] ?? '') === 'Sales') {
            $maxOverdue = $this->getMaxOverdueDays($mitra->id);
            if ($maxOverdue > 60) {
                $needsApproval = true;
            }
        }

        // Force Draft status if approval is needed
        if ($needsApproval) {
            $invoiceData['status_dok'] = 'Draft';
            $invoiceData['status_pembayaran'] = 'Draft';
            $invoiceData['keterangan'] = ($invoiceData['keterangan'] ?? '')."\nAuto-Archived: Umur nota > 60 hari ($maxOverdue hari). Perlu ACC Owner.";
            $warningMessage = "Harus ACC Owner, dikarenakan umur nota ($maxOverdue hari). Invoice masuk ke Arsip.";
        }

        // Validate Stock Location if provided
        if (! empty($request->invoice['stock_location_id'])) {
            $location = StockLocation::where('id', $request->invoice['stock_location_id'])
                ->where('office_id', session('active_office_id'))
                ->first();

            if (! $location) {
                return apiResponse(false, 'Lokasi stok tidak valid untuk outlet ini', null, null, 422);
            }
        }

        return DB::transaction(function () use ($request, $invoiceData, $warningMessage, $needsApproval) {

            // $invoiceData = $request->invoice; // Removed because defined above
            $invoiceData['office_id'] = session('active_office_id');
            $invoice = Invoice::create($invoiceData);

            if ($needsApproval) {
                InvoiceApprovalDetail::create([
                    'invoice_id' => $invoice->id,
                    'office_id' => session('active_office_id'),
                    'requested_by' => auth()->id(),
                    'status' => 'pending',
                ]);
            }

            foreach ($request->items as $itemData) {

                $itemData['invoice_id'] = $invoice->id;

                $item = InvoiceItem::create($itemData);

                // FIFO Stock Tracking
                // Skip stock recording if approval is needed
                if (! $needsApproval && $item->product && $item->product->track_stock) {
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
            // BUT: Defer journal for Sales Invoices that need approval
            if ($invoice->tipe_invoice === 'Purchase') {
                $this->journalService->recordPurchaseInvoice($invoice->fresh(['items.product']));
            } elseif ($invoice->tipe_invoice === 'Sales') {
                if (! $needsApproval) {
                    // Check if journal already exists to prevent duplicate entries
                    $hasJournal = Journal::where('nomor_referensi', "Sales Invoice #{$invoice->nomor_invoice}")->exists();
                    if (! $hasJournal) {
                        $this->journalService->recordSalesInvoice($invoice->fresh(['items.product']));
                    }
                }
            }

            $msg = 'Invoice berhasil dibuat';
            if ($warningMessage) {
                $msg .= '. '.$warningMessage;
            }

            return apiResponse(true, $msg, $invoice->load('items'), null, 201);
        });
    }

    public function accAdmin(Request $request)
    {
        $officeId = session('active_office_id');

        $query = Invoice::with(['mitra', 'items', 'approvals.requestedBy', 'approvals.processedBy'])
            ->withSum('payment', 'jumlah_bayar')
            ->where('tipe_invoice', 'Sales')
            ->where('office_id', $officeId)
            ->has('approvals');

        if ($request->tipe_invoice) {
            $query->where('tipe_invoice', $request->tipe_invoice);
        }

        if ($request->mitra_id) {
            $query->where('mitra_id', $request->mitra_id);
        }

        if ($request->date_from) {
            $query->whereDate('tgl_invoice', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('tgl_invoice', '<=', $request->date_to);
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
        if ($perPage >= 1000) {
            $data = $query->latest()->get();
        } else {
            $data = $query->latest()->paginate($perPage)->withQueryString();
        }

        return apiResponse(true, 'Data invoice approval berhasil diambil', $data, null, 200);
    }

    public function approve($id)
    {
        return DB::transaction(function () use ($id) {
            $invoice = Invoice::where('office_id', session('active_office_id'))->find($id);

            if (! $invoice) {
                return apiResponse(false, 'Invoice tidak ditemukan', null, null, 404);
            }

            if ($invoice->payment()->exists()) {
                return apiResponse(false, 'Invoice tidak dapat disetujui karena sudah ada kuitansi/pembayaran.', null, null, 422);
            }

            $approval = InvoiceApprovalDetail::where('invoice_id', $id)
                ->where('status', 'pending')
                ->first();

            if (! $approval) {
                return apiResponse(false, 'Request approval tidak ditemukan', null, null, 404);
            }

            $approval->update([
                'status' => 'approved',
                'processed_by' => auth()->id(),
            ]);

            // Sync invoice status
            $invoice->update(['status_dok' => 'Approved']);

            // Record Stock changes upon Approval (for Sales invoices that needed approval)
            if ($invoice->tipe_invoice === 'Sales') {
                foreach ($invoice->items as $item) {
                    if ($item->product && $item->product->track_stock) {
                        $this->stockService->recordOut(
                            $item->produk_id,
                            $item->qty,
                            $invoice->stock_location_id,
                            'Sales',
                            $invoice->id,
                            "Sales Invoice #{$invoice->nomor_invoice} (Approved)"
                        );
                    }
                }
            }

            // Record Journal Entry upon Approval (if not already recorded)
            if ($invoice->tipe_invoice === 'Sales') {
                // To be safe, check if journal already exists (optional but good for idempotency)
                $hasJournal = Journal::where('nomor_referensi', "Sales Invoice #{$invoice->nomor_invoice}")->exists();
                if (! $hasJournal) {
                    $this->journalService->recordSalesInvoice($invoice->fresh(['items.product']));
                }
            }

            return apiResponse(true, 'Invoice berhasil disetujui');
        });
    }

    public function reject($id)
    {
        return DB::transaction(function () use ($id) {
            $invoice = Invoice::where('office_id', session('active_office_id'))->find($id);

            if (! $invoice) {
                return apiResponse(false, 'Invoice tidak ditemukan', null, null, 404);
            }

            if ($invoice->payment()->exists()) {
                return apiResponse(false, 'Invoice tidak dapat ditolak karena sudah ada kuitansi/pembayaran.', null, null, 422);
            }

            $approval = InvoiceApprovalDetail::where('invoice_id', $id)
                ->where('status', 'pending')
                ->first();

            if (! $approval) {
                return apiResponse(false, 'Request approval tidak ditemukan', null, null, 404);
            }

            $approval->update([
                'status' => 'rejected',
                'processed_by' => auth()->id(),
            ]);

            // Do NOT change stock when rejected - rejected invoices are not valid purchases
            // Only approved and withdrawn approved invoices should affect stock

            // Sync invoice status
            $invoice->update(['status_dok' => 'Rejected']);

            return apiResponse(true, 'Invoice ditolak');
        });
    }

    public function withdraw($id)
    {
        return DB::transaction(function () use ($id) {
            $invoice = Invoice::where('office_id', session('active_office_id'))->find($id);

            if (! $invoice) {
                return apiResponse(false, 'Invoice tidak ditemukan', null, null, 404);
            }

            if ($invoice->payment()->exists()) {
                return apiResponse(false, 'Persetujuan tidak dapat ditarik karena sudah ada kuitansi/pembayaran.', null, null, 422);
            }

            $approval = InvoiceApprovalDetail::where('invoice_id', $id)
                ->whereIn('status', ['approved', 'rejected'])
                ->latest()
                ->first();

            if (! $approval) {
                return apiResponse(false, 'Data persetujuan tidak ditemukan.', null, null, 404);
            }

            $previousStatus = $approval->status;

            $approval->update([
                'status' => 'pending',
                'processed_by' => null,
            ]);

            // Handle stock and journal based on previous status
            if ($previousStatus === 'approved') {
                // If withdrawing approval, reverse stock and journal
                foreach ($invoice->items as $item) {
                    if ($item->product && $item->product->track_stock) {
                        if ($invoice->tipe_invoice === 'Sales') {
                            // Stock was deducted when approved, now return it
                            $this->stockService->recordIn(
                                $item->produk_id,
                                $item->qty,
                                $item->product->harga_beli,
                                $invoice->stock_location_id,
                                'Sales Return (Approval Withdrawn)',
                                $invoice->id,
                                "Stock Return (Approval Withdrawn): #{$invoice->nomor_invoice}"
                            );
                        } elseif ($invoice->tipe_invoice === 'Purchase') {
                            // Stock was added when approved, now deduct it
                            $this->stockService->recordOut(
                                $item->produk_id,
                                $item->qty,
                                $invoice->stock_location_id,
                                'Purchase Cancel (Approval Withdrawn)',
                                $invoice->id,
                                "Stock Deduct (Purchase Approval Withdrawn): #{$invoice->nomor_invoice}"
                            );
                        }
                    }
                }

                // Reverse journal entry
                $this->journalService->deleteInvoiceJournal($invoice);
            }
            // If withdrawing rejection, do NOT touch stock (as per user requirement)
            // Stock was already returned when rejected, and should stay that way

            $invoice->update(['status_dok' => 'Draft']);

            return apiResponse(true, 'Persetujuan berhasil ditarik');
        });
    }

    public function archive($id)
    {
        return DB::transaction(function () use ($id) {
            $invoice = Invoice::where('office_id', session('active_office_id'))->findOrFail($id);

            // Check if already has payments
            if ($invoice->payment()->count() > 0) {
                return apiResponse(false, 'Invoice tidak dapat diarsipkan karena sudah ada kuitansi/pembayaran.', null, null, 422);
            }

            // 1. Reverse COA
            $this->journalService->deleteInvoiceJournal($invoice);

            // 2. Reverse Stock
            foreach ($invoice->items as $item) {
                if ($item->product && $item->product->track_stock) {
                    if ($invoice->tipe_invoice === 'Sales') {
                        $this->stockService->recordIn(
                            $item->produk_id,
                            $item->qty,
                            $item->product->harga_beli,
                            $invoice->stock_location_id,
                            'Sales Return (Archived)',
                            $invoice->id,
                            "Stock Return (Invoice Archived): #{$invoice->nomor_invoice}"
                        );
                    } elseif ($invoice->tipe_invoice === 'Purchase') {
                        $this->stockService->recordOut(
                            $item->produk_id,
                            $item->qty,
                            $invoice->stock_location_id,
                            'Purchase Cancel (Archived)',
                            $invoice->id,
                            "Stock Deduct (Purchase Invoice Archived): #{$invoice->nomor_invoice}"
                        );
                    }
                }
            }

            // 3. Update Status
            $invoice->update([
                'status_dok' => 'Draft',
                'status_pembayaran' => 'Draft',
            ]);

            return apiResponse(true, 'Invoice berhasil diarsipkan');
        });
    }

    public function unarchive($id)
    {
        return DB::transaction(function () use ($id) {
            $invoice = Invoice::where('office_id', session('active_office_id'))->findOrFail($id);

            // 1. Update Status to Approved (as per new requirement)
            $invoice->update([
                'status_dok' => 'Approved',
                'status_pembayaran' => 'Unpaid',
            ]);

            // 2. Redo Stock
            foreach ($invoice->items as $item) {
                if ($item->product && $item->product->track_stock) {
                    if ($invoice->tipe_invoice === 'Sales') {
                        $this->stockService->recordOut(
                            $item->produk_id,
                            $item->qty,
                            $invoice->stock_location_id,
                            'Sales',
                            $invoice->id,
                            "Sales Invoice (Unarchived) #{$invoice->nomor_invoice}"
                        );
                    } elseif ($invoice->tipe_invoice === 'Purchase') {
                        $this->stockService->recordIn(
                            $item->produk_id,
                            $item->qty,
                            $item->harga_satuan,
                            $invoice->stock_location_id,
                            'Purchase',
                            $invoice->id,
                            "Purchase Invoice (Unarchived) #{$invoice->nomor_invoice}"
                        );
                    }
                }
            }

            // 3. Redo COA
            if ($invoice->tipe_invoice === 'Purchase') {
                $this->journalService->recordPurchaseInvoice($invoice->fresh(['items.product']));
            } elseif ($invoice->tipe_invoice === 'Sales') {
                $this->journalService->recordSalesInvoice($invoice->fresh(['items.product']));
            }

            return apiResponse(true, 'Invoice berhasil dikembalikan dari arsip');
        });
    }

    public function restore($id)
    {
        return DB::transaction(function () use ($id) {
            $invoice = Invoice::withTrashed()
                ->where('office_id', session('active_office_id'))
                ->findOrFail($id);

            // 1. Restore the SoftDelete
            $invoice->restore();

            // 2. Perform Unarchive logic (Ensure Approved/Unpaid & redo stock/journals)
            // This is exactly what unarchive does, so we ensure consistency
            $invoice->update([
                'status_dok' => 'Approved',
                'status_pembayaran' => 'Unpaid',
            ]);

            // Redo Stock
            foreach ($invoice->items as $item) {
                if ($item->product && $item->product->track_stock) {
                    if ($invoice->tipe_invoice === 'Sales') {
                        $this->stockService->recordOut(
                            $item->produk_id,
                            $item->qty,
                            $invoice->stock_location_id,
                            'Sales',
                            $invoice->id,
                            "Sales Invoice (Restored) #{$invoice->nomor_invoice}"
                        );
                    } elseif ($invoice->tipe_invoice === 'Purchase') {
                        $this->stockService->recordIn(
                            $item->produk_id,
                            $item->qty,
                            $item->harga_satuan,
                            $invoice->stock_location_id,
                            'Purchase',
                            $invoice->id,
                            "Purchase Invoice (Restored) #{$invoice->nomor_invoice}"
                        );
                    }
                }
            }

            // Redo COA
            if ($invoice->tipe_invoice === 'Purchase') {
                $this->journalService->recordPurchaseInvoice($invoice->fresh(['items.product']));
            } elseif ($invoice->tipe_invoice === 'Sales') {
                $this->journalService->recordSalesInvoice($invoice->fresh(['items.product']));
            }

            return apiResponse(true, 'Invoice berhasil dipulihkan');
        });
    }

    public function forceDestroy($id)
    {
        try {
            $invoice = Invoice::onlyTrashed()
                ->where('office_id', session('active_office_id'))
                ->findOrFail($id);

            // Journals and Stock should have been reversed during soft delete or archive.
            // But for absolute safety, ensure Journals are gone (Stock cannot be easily "force reversed" if record is gone)
            $this->journalService->deleteInvoiceJournal($invoice);

            $invoice->forceDelete();

            return apiResponse(true, 'Invoice berhasil dihapus permanen');
        } catch (\Exception $e) {
            return apiResponse(false, 'Gagal menghapus invoice permanen', null, $e->getMessage(), 500);
        }
    }

    public function overdueAdmin(Request $request)
    {
        $officeId = session('active_office_id');

        Invoice::where('tipe_invoice', 'Sales')
            ->where('office_id', $officeId)
            ->where('status_dok', 'Approved')
            ->where('status_pembayaran', '!=', 'Paid')
            ->whereDate('tgl_invoice', '<=', Carbon::now()->subDays(60)->toDateString())
            ->update(['status_pembayaran' => 'Overdue']);

        $query = Invoice::with(['mitra', 'items', 'approvals.requestedBy', 'approvals.processedBy'])
            ->withSum('payment', 'jumlah_bayar')
            ->where('tipe_invoice', 'Sales')
            ->where('office_id', $officeId)
            ->where('status_pembayaran', 'Overdue');

        if ($request->mitra_id) {
            $query->where('mitra_id', $request->mitra_id);
        }

        if ($request->date_from) {
            $query->whereDate('tgl_invoice', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('tgl_invoice', '<=', $request->date_to);
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
        if ($perPage >= 1000) {
            $data = $query->latest()->get();
        } else {
            $data = $query->latest()->paginate($perPage)->withQueryString();
        }

        return apiResponse(true, 'Data invoice overdue berhasil diambil', $data, null, 200);
    }

    public function approveOverdue($id)
    {
        return DB::transaction(function () use ($id) {
            $invoice = Invoice::where('office_id', session('active_office_id'))->find($id);

            if (! $invoice) {
                return apiResponse(false, 'Invoice tidak ditemukan', null, null, 404);
            }

            if ($invoice->status_pembayaran !== 'Overdue') {
                return apiResponse(false, 'Invoice tidak berstatus Overdue', null, null, 422);
            }

            $invoice->update([
                'status_pembayaran' => 'Unpaid',
                'tgl_invoice' => Carbon::now()->toDateString(),
                'tgl_jatuh_tempo' => Carbon::now()->addMonth()->toDateString(),
            ]);

            return apiResponse(true, 'Status Invoice Overdue berhasil diubah menjadi Unpaid dengan tempo 1 bulan.');
        });
    }

    public function rejectOverdue($id)
    {
        return DB::transaction(function () use ($id) {
            $invoice = Invoice::where('office_id', session('active_office_id'))->find($id);

            if (! $invoice) {
                return apiResponse(false, 'Invoice tidak ditemukan', null, null, 404);
            }

            if ($invoice->status_pembayaran !== 'Overdue') {
                return apiResponse(false, 'Invoice tidak berstatus Overdue', null, null, 422);
            }

            $invoice->update(['status_dok' => 'Rejected']);

            return apiResponse(true, 'Invoice Overdue telah ditolak (Rejected).');
        });
    }
}
