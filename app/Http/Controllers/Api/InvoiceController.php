<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\ActivityLog;
use App\Models\InvoiceItem;
use App\Models\InvoiceItemTaxe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['mitra', 'items.product', 'items.taxes', 'payment'])
            ->withSum('payment', 'jumlah_bayar');

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
        $data = Invoice::with(['mitra', 'items.product', 'items.taxes', 'payment'])
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

        $invoice = Invoice::create($request->all());

        $this->logActivity('Create', 'invoices', $invoice->id, null, $invoice);

        return apiResponse(true, 'Invoice berhasil dibuat', $invoice, null, 201);
    }

    public function update(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $invoice = Invoice::find($id);
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
                            InvoiceItemTaxe::create([
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
        $invoice = Invoice::find($id);
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
            'items.*.taxes.*.tax_id' => 'required|exists:taxes,id',
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        return DB::transaction(function () use ($request) {

            $invoice = Invoice::create($request->invoice);

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

                $this->logActivity(
                    'Create',
                    'invoice_items',
                    $item->id,
                    null,
                    $item
                );

                if (!empty($itemData['taxes'])) {
                    foreach ($itemData['taxes'] as $taxData) {

                        $tax = InvoiceItemTaxe::create([
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

            return apiResponse(true, 'Invoice berhasil dibuat lengkap', [
                'invoice' => $invoice->load('items.taxes.tax')
            ], null, 201);
        });
    }

    private function logActivity($tindakan, $tabel, $dataId, $before, $after)
    {
        ActivityLog::create([
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
