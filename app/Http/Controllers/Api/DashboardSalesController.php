<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

            $query = DB::table('invoices')
                ->join('mitras', 'invoices.mitra_id', '=', 'mitras.id')
                ->where('invoices.tipe_invoice', 'Sales')
                ->where('invoices.office_id', $officeId)
                ->whereBetween('invoices.tgl_invoice', [$startDate, $endDate])
                ->whereNull('invoices.deleted_at');

            if ($mitraId) {
                $query->where('invoices.mitra_id', $mitraId);
            }

            if ($status) {
                if ($status === 'NOT_PAID') {
                    $query->where('invoices.status_pembayaran', '!=', 'Paid');
                } else {
                    $query->where('invoices.status_pembayaran', $status);
                }
            }

            $summary = (clone $query)->select(
                DB::raw('SUM(invoices.total_akhir) as total_sales'),
                DB::raw('SUM(CASE WHEN invoices.status_pembayaran != "Paid" AND mitras.is_cash_customer = 0 THEN invoices.total_akhir ELSE 0 END) as total_piutang'),
                DB::raw('ROUND(AVG(CASE WHEN invoices.status_pembayaran != "Paid" AND mitras.is_cash_customer = 0 THEN DATEDIFF(NOW(), invoices.tgl_invoice) ELSE NULL END)) as avg_aging')
            )->first();

            $invoices = $query->select(
                'invoices.id',
                'invoices.nomor_invoice',
                'invoices.tgl_invoice',
                'invoices.total_akhir',
                'invoices.status_pembayaran',
                'mitras.nama as nama_pelanggan',
                DB::raw('DATEDIFF(NOW(), invoices.tgl_invoice) as umur_nota')
            )
                ->orderBy('invoices.tgl_invoice', 'desc')
                ->paginate($perPage)
                ->withQueryString();

            $listMitra = DB::table('mitras')
                ->whereIn('tipe_mitra', ['Client', 'Both'])
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
        $invoice = DB::table('invoices')
            ->where('id', $id)
            ->where('office_id', session('active_office_id'))
            ->first();

        if (! $invoice) {
            return response()->json(['success' => false, 'message' => 'Invoice not found or access denied'], 404);
        }

        $items = DB::table('invoice_items')
            ->leftJoin('products', 'invoice_items.produk_id', '=', 'products.id')
            ->where('invoice_id', $id)
            ->select('invoice_items.*', 'products.nama_produk', 'products.sku_kode')
            ->get();

        return response()->json(['success' => true, 'data' => $items]);
    }
}

