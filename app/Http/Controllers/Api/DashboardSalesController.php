<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Partner;
use App\Models\Product;
use Illuminate\Http\Request;
use Throwable;

class DashboardSalesController extends Controller
{
    public function index(Request $request)
    {
        try {
            $officeId = session('active_office_id');

            $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
            $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
            $mitraId = $request->input('mitra_id');
            $status = $request->input('status');
            $perPage = (int) ($request->input('per_page', 10));

            // Build base query using Eloquent
            $query = Invoice::with(['mitra'])
                ->where('tipe_invoice', 'Sales')
                ->where('office_id', $officeId)
                ->whereBetween('tgl_invoice', [$startDate, $endDate]);

            if ($mitraId) {
                $query->where('mitra_id', $mitraId);
            }

            if ($status) {
                if ($status === 'NOT_PAID') {
                    $query->where('status_pembayaran', '!=', 'Paid');
                } else {
                    $query->where('status_pembayaran', $status);
                }
            }

            // Calculate summary using Eloquent collections
            $invoicesForSummary = $query->get();
            $summary = (object) [
                'total_sales' => $invoicesForSummary->sum('total_akhir'),
                'total_piutang' => $invoicesForSummary->filter(function ($invoice) {
                    return $invoice->status_pembayaran !== 'Paid' && $invoice->mitra && $invoice->mitra->is_cash_customer === false;
                })->sum('total_akhir'),
                'avg_aging' => $invoicesForSummary->filter(function ($invoice) {
                    return $invoice->status_pembayaran !== 'Paid' && $invoice->mitra && $invoice->mitra->is_cash_customer === false;
                })->avg(function ($invoice) {
                    return now()->diffInDays($invoice->tgl_invoice);
                })
            ];

            // Build invoices query with calculated aging
            $invoicesQuery = clone $query;
            $invoicesCollection = $invoicesQuery->orderBy('tgl_invoice', 'desc')->get();
            
            // Add calculated umur_nota to collection
            $invoicesWithAging = $invoicesCollection->map(function ($invoice) {
                $invoice->umur_nota = now()->diffInDays($invoice->tgl_invoice);
                return $invoice;
            });

            // Handle pagination
            $perPage = (int) ($request->get('per_page', 10));
            if ($perPage >= 1000) {
                $invoices = $invoicesWithAging;
            } else {
                // Manual pagination for collection
                $currentPage = request()->get('page', 1);
                $offset = ($currentPage - 1) * $perPage;
                $itemsForCurrentPage = $invoicesWithAging->slice($offset, $perPage)->values();
                
                $invoices = new \Illuminate\Pagination\LengthAwarePaginator(
                    $itemsForCurrentPage,
                    $invoicesWithAging->count(),
                    $perPage,
                    $currentPage,
                    [
                        'path' => request()->url(),
                        'query' => request()->query(),
                    ]
                );
            }

            // Get list of mitra using Eloquent
            $listMitra = Partner::whereIn('tipe_mitra', ['Client', 'Both'])
                ->where('office_id', $officeId)
                ->orderBy('nama', 'asc')
                ->get(['id', 'nama']);

            return response()->json([
                'success' => true,
                'data' => [
                    'summary' => $summary,
                    'invoices' => $invoices,
                    'listMitra' => $listMitra,
                    'filters' => [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'mitra_id' => $mitraId,
                        'status' => $status,
                    ],
                ],
            ]);
        } catch (Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function detail($id)
    {
        $invoice = Invoice::where('id', $id)
            ->where('office_id', session('active_office_id'))
            ->first();

        if (! $invoice) {
            return response()->json(['success' => false, 'message' => 'Invoice not found or access denied'], 404);
        }

        $items = InvoiceItem::with(['product' => function ($query) {
                $query->select('id', 'nama_produk', 'sku_kode');
            }])
            ->where('invoice_id', $id)
            ->get()
            ->map(function ($item) {
                // Add product data to the item for consistent structure
                $item->nama_produk = $item->product ? $item->product->nama_produk : $item->nama_produk_manual;
                $item->sku_kode = $item->product ? $item->product->sku_kode : null;
                unset($item->product); // Remove the relationship to match original structure
                return $item;
            });

        return response()->json(['success' => true, 'data' => $items]);
    }
}

