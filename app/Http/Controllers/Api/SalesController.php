<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    private $views = 'Sales.';

    public function index()
    {
        $user = auth()->user();
        $officeId = session('active_office_id');

        $canSelectSales = false;
        if ($user && $user->id == 1) {
            $canSelectSales = true;
        } elseif ($user && $officeId) {
            $roleRow = DB::table('user_office_roles')
                ->join('roles', 'user_office_roles.role_id', '=', 'roles.id')
                ->where('user_office_roles.user_id', $user->id)
                ->where('user_office_roles.office_id', $officeId)
                ->select('roles.id as role_id', 'roles.name as role_name')
                ->first();

            if ($roleRow && strtolower($roleRow->role_name) === 'superadmin') {
                $canSelectSales = true;
            } elseif ($roleRow) {
                $rolePermCount = DB::table('role_permissions')->where('role_id', $roleRow->role_id)->count();
                $totalPermCount = DB::table('permissions')->count();
                if ($totalPermCount > 0 && $rolePermCount >= $totalPermCount) {
                    $canSelectSales = true;
                }
            }
        }

        $users = collect();
        if ($officeId) {
            $users = DB::table('users')
                ->join('user_office_roles', 'users.id', '=', 'user_office_roles.user_id')
                ->join('role_permissions', 'user_office_roles.role_id', '=', 'role_permissions.role_id')
                ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
                ->where('user_office_roles.office_id', $officeId)
                ->where(function ($q) {
                    $q->where('permissions.name', 'sales')
                        ->orWhere('permissions.name', 'like', 'sales.%');
                })
                ->select('users.id', 'users.name')
                ->distinct()
                ->orderBy('users.name')
                ->get();
        }

        return view($this->views.'index', compact('users', 'canSelectSales'));
    }

    public function export(Request $request)
    {
        $query = Invoice::with(['mitra', 'payment'])
            ->where('office_id', session('active_office_id'))
            ->where('tipe_invoice', 'Sales');

        if ($request->tab_status === 'trash') {
            $query->onlyTrashed();
        } elseif ($request->tab_status === 'archive') {
            $query->where('status_dok', 'Archived');
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

        $invoices = $query->latest()->get();

        return view($this->views.'export', compact('invoices'));
    }

    public function massPrint(Request $request)
    {
        $salesId = $request->sales_id;
        $period = $request->period;
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        // Validate input
        if (!$salesId || !$period) {
            return response()->json(['error' => 'Sales ID and Period are required'], 400);
        }

        if ($period === 'custom' && (!$dateFrom || !$dateTo)) {
            return response()->json(['error' => 'Date from and Date to are required for custom period'], 400);
        }

        // Calculate date range based on period
        $now = now();
        switch ($period) {
            case 'this_month':
                $dateFrom = $now->startOfMonth()->format('Y-m-d');
                $dateTo = $now->endOfMonth()->format('Y-m-d');
                break;
            case 'last_month':
                $dateFrom = $now->subMonth()->startOfMonth()->format('Y-m-d');
                $dateTo = $now->subMonth()->endOfMonth()->format('Y-m-d');
                break;
            case 'this_quarter':
                $quarter = ceil($now->month / 3);
                $dateFrom = $now->startOfQuarter()->format('Y-m-d');
                $dateTo = $now->endOfQuarter()->format('Y-m-d');
                break;
            case 'this_year':
                $dateFrom = $now->startOfYear()->format('Y-m-d');
                $dateTo = $now->endOfYear()->format('Y-m-d');
                break;
        }

        // Get sales person info
        $salesPerson = null;
        if ($salesId != 0) {
            $salesPerson = DB::table('users')->where('id', $salesId)->first();
        }

        // Get invoices with payments
        $invoices = DB::table('invoices as i')
            ->leftJoin('mitras as m', 'i.mitra_id', '=', 'm.id')
            ->leftJoin('payments as p', 'i.id', '=', 'p.invoice_id')
            ->where('i.office_id', session('active_office_id'))
            ->where('i.tipe_invoice', 'Sales')
            ->where('i.sales_id', $salesId)
            ->whereDate('i.tgl_invoice', '>=', $dateFrom)
            ->whereDate('i.tgl_invoice', '<=', $dateTo)
            ->select(
                'i.id',
                'i.nomor_invoice',
                'i.tgl_invoice',
                'i.total_akhir',
                'm.nama as mitra_nama',
                'p.tgl_pembayaran as payment_date',
                'p.jumlah_bayar as payment_amount',
                'p.catatan as payment_note',
                'p.nomor_pembayaran as no_receipt'
            )
            ->orderBy('i.tgl_invoice')
            ->get();

        // Calculate totals
        $totalInvoices = $invoices->sum('total_akhir');
        $totalPayments = $invoices->sum('payment_amount');
        $totalRemaining = $totalInvoices - $totalPayments;

        // Group payments by date for Step 1
        $paymentsByDate = $invoices
            ->whereNotNull('payment_date')
            ->groupBy('payment_date')
            ->map(function ($group) {
                return [
                    'date' => $group->first()->payment_date,
                    'total' => $group->sum('payment_amount'),
                    'customers' => $group->pluck('mitra_nama')->unique(),
                    'receipts' => $group->pluck('no_receipt')->filter()
                ];
            })
            ->sortBy('date');

        // Get monthly data for charts
        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthInvoices = $invoices->filter(function ($inv) use ($i) {
                return date('n', strtotime($inv->tgl_invoice)) == $i;
            });
            $monthlyData[] = $monthInvoices->sum('total_akhir');
        }

        return view($this->views.'mass-print', compact(
            'salesPerson',
            'period',
            'dateFrom',
            'dateTo',
            'invoices',
            'paymentsByDate',
            'totalInvoices',
            'totalPayments',
            'totalRemaining',
            'monthlyData'
        ));
    }
}
