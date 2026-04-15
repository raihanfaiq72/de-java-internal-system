<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryOrderFleet;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Office;
use App\Models\Payment;
use App\Models\Product;
use App\Models\UserOfficeRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class DashboardController extends Controller
{
    public function summary()
    {
        try {
            $officeId = session('active_office_id');
            $office = $officeId ? Office::find($officeId) : null;
            $user = Auth::user();

            $userRoleRel = UserOfficeRole::with('role')
                ->where('user_id', $user->id)
                ->where('office_id', $officeId)
                ->first();

            $roleName = $userRoleRel && $userRoleRel->role ? $userRoleRel->role->name : 'User';

            $data = [
                'office' => $office ? [
                    'id' => $office->id,
                    'name' => $office->name,
                    'code' => $office->code,
                ] : null,
                'roleName' => $roleName,
                'isDriver' => false,
                'stats' => [],
                'recentTransactions' => [],
                'driverJobs' => [],
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                ],
            ];

            if (stripos($roleName, 'Driver') !== false || stripos($roleName, 'Kurir') !== false) {
                $data['isDriver'] = true;

                $driverJobs = DeliveryOrderFleet::with(['deliveryOrder'])
                    ->where('driver_id', $user->id)
                    ->whereIn('status', ['assigned', 'in_transit'])
                    ->orderBy('created_at', 'desc')
                    ->get();

                $data['driverJobs'] = $driverJobs->map(function ($job) {
                    return [
                        'delivery_order_id' => $job->delivery_order_id,
                        'status' => $job->status,
                        'created_at' => $job->created_at,
                        'delivery_order_number' => $job->deliveryOrder->delivery_order_number ?? null,
                    ];
                });

                $data['stats']['completed_today'] = DeliveryOrderFleet::where('driver_id', $user->id)
                    ->where('status', 'completed')
                    ->whereDate('updated_at', today())
                    ->count();

                $data['stats']['active_jobs'] = $driverJobs->count();
            } else {
                // Main Dashboard Stats
                $data['stats']['revenue'] = Invoice::where('office_id', $officeId)
                    ->where('tipe_invoice', 'Sales')
                    ->where('status_pembayaran', 'Paid')
                    ->sum('total_akhir');

                $data['stats']['piutang_usaha'] = Invoice::where('office_id', $officeId)
                    ->where('tipe_invoice', 'Sales')
                    ->where('status_pembayaran', '!=', 'Paid')
                    ->where('status_pembayaran', '!=', 'Draft') // Exclude Archived
                    ->get()
                    ->sum(function ($inv) {
                        return $inv->total_akhir - $inv->payment()->sum('jumlah_bayar');
                    });

                $data['stats']['hutang_usaha'] = Invoice::where('office_id', $officeId)
                    ->where('tipe_invoice', 'Purchase')
                    ->where('status_pembayaran', '!=', 'Paid')
                    ->where('status_pembayaran', '!=', 'Draft') // Exclude Archived
                    ->get()
                    ->sum(function ($inv) {
                        return $inv->total_akhir - $inv->payment()->sum('jumlah_bayar');
                    });

                $totalIncome = Payment::where('office_id', $officeId)
                    ->whereHas('invoice', fn ($q) => $q->where('tipe_invoice', 'Sales'))
                    ->sum('jumlah_bayar');
                $totalOutcome = Payment::where('office_id', $officeId)
                    ->whereHas('invoice', fn ($q) => $q->where('tipe_invoice', 'Purchase'))
                    ->sum('jumlah_bayar');
                $totalExpenses = Expense::where('office_id', $officeId)->sum('jumlah');

                $data['stats']['saldo_aktif'] = $totalIncome - $totalOutcome - $totalExpenses;

                // Accurate Net Profit calculation based on GL (matching ReportController@profitAndLoss)
                // $firstDayOfMonth = date('2026-01-01');
                // $now = date('Y-m-d');
                $movements = DB::table('journal_details')
                    ->join('journals', 'journal_details.journal_id', '=', 'journals.id')
                    ->where('journals.office_id', $officeId)
                    ->whereNull('journals.deleted_at')
                    ->whereNull('journal_details.deleted_at')
                    // ->whereBetween('journals.tgl_jurnal', [$firstDayOfMonth, $now])
                    ->select(
                        'journal_details.akun_id',
                        DB::raw('SUM(journal_details.debit) as total_debit'),
                        DB::raw('SUM(journal_details.kredit) as total_credit')
                    )
                    ->groupBy('journal_details.akun_id')
                    ->get()
                    ->keyBy('akun_id');

                $targetGroups = [4000, 5000, 6000, 6800, 7001, 8001, 9000];

                $groups = DB::table('coa_group')
                    ->join('coa_type', 'coa_group.id', '=', 'coa_type.kelompok_id')
                    ->join('chart_of_accounts', 'coa_type.id', '=', 'chart_of_accounts.tipe_id')
                    ->where('coa_group.office_id', $officeId)
                    ->whereIn('coa_group.kode_kelompok', $targetGroups)
                    ->whereNull('coa_group.deleted_at')
                    ->whereNull('coa_type.deleted_at')
                    ->whereNull('chart_of_accounts.deleted_at')
                    ->select(
                        'coa_group.kode_kelompok',
                        'chart_of_accounts.id as coa_id'
                    )
                    ->get();

                $totalRevenue = 0;
                $totalExpense = 0;

                foreach ($groups as $row) {
                    $groupId = $row->kode_kelompok;
                    $coaId = $row->coa_id;

                    $debit = 0;
                    $credit = 0;
                    if (isset($movements[$coaId])) {
                        $debit = $movements[$coaId]->total_debit;
                        $credit = $movements[$coaId]->total_credit;
                    }

                    if (in_array($groupId, [4000, 7001])) {
                        $totalRevenue += ($credit - $debit);
                    } else {
                        $totalExpense += ($debit - $credit);
                    }
                }

                $data['stats']['laba_bersih'] = $totalRevenue - $totalExpense;

                // dd($data['stats']['laba_bersih'], $totalRevenue, $totalExpense);

                $data['stats']['new_orders'] = Invoice::where('office_id', $officeId)
                    ->where('tipe_invoice', 'Sales')
                    ->where('status_dok', 'Approved') // Filter by Approved
                    ->whereDate('created_at', today())
                    ->count();

                $data['stats']['low_stock'] = Product::where('office_id', $officeId)
                    ->where('track_stock', 1)
                    ->where('qty', '<=', 10)
                    ->count();

                $recent = Invoice::with('mitra')
                    ->where('office_id', $officeId)
                    ->where('tipe_invoice', 'Sales')
                    ->where('status_dok', 'Approved') // Only show approved transactions
                    ->latest()
                    ->limit(10)
                    ->get();

                $data['recentTransactions'] = $recent->map(function ($tx) {
                    return [
                        'nomor_invoice' => $tx->nomor_invoice,
                        'tipe_invoice' => $tx->tipe_invoice,
                        'mitra' => [
                            'nama' => $tx->mitra->nama ?? null,
                        ],
                        'total_akhir' => $tx->total_akhir,
                        'status_pembayaran' => $tx->status_pembayaran,
                        'created_at' => $tx->created_at,
                    ];
                });
            }

            return response()->json(['success' => true, 'data' => $data]);
        } catch (Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
