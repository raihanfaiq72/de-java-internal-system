<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryOrderFleet;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Office;
use App\Models\Payment;
use App\Models\Product;
use App\Models\StockMutation;
use App\Models\UserOfficeRole;
use Carbon\Carbon;
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

            // Range Filtering Logic
            $range = request()->query('range', 'month');
            $start = Carbon::now();
            $end = Carbon::now()->endOfDay();

            switch ($range) {
                case 'today':
                    $start = Carbon::now()->startOfDay();
                    break;
                case 'week':
                    $start = Carbon::now()->startOfWeek();
                    break;
                case 'month':
                    $start = Carbon::now()->startOfMonth();
                    break;
                case 'year':
                    $start = Carbon::now()->startOfYear();
                    break;
                case 'all':
                    $start = Carbon::parse('2020-01-01'); // Long ago
                    break;
                default:
                    $start = Carbon::now()->startOfMonth();
                    break;
            }

            // Calculate Previous Period for Comparison (Apples-to-Apples for Revenue)
            $start_prev = $start->copy();
            switch ($range) {
                case 'today': $start_prev = $start->copy()->subDay(); break;
                case 'week': $start_prev = $start->copy()->subWeek(); break;
                case 'month': $start_prev = $start->copy()->subMonth(); break;
                case 'year': $start_prev = $start->copy()->subYear(); break;
                default: $start_prev = $start->copy()->subMonth(); break;
            }
            $end_prev = $start->copy()->subSecond();
            
            // For revenue, we compare the exact same duration from the start
            $elapsedSeconds = now()->diffInSeconds($start);
            $end_prev_revenue = $start_prev->copy()->addSeconds($elapsedSeconds);

            $data = [
                'office' => $office ? [
                    'id' => $office->id,
                    'name' => $office->name,
                    'code' => $office->code,
                ] : null,
                'roleName' => $roleName,
                'range' => $range,
                'isDriver' => false,
                'stats' => [],
                'recentTransactions' => [],
                'activityLog' => [],
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                ],
                'serverTime' => now()->toISOString(),
            ];

            // 1. Adaptive Sales Trend (Dynamic Chart Scale)
            $chartData = collect();
            $query = Invoice::where('office_id', $officeId)
                ->where('tipe_invoice', 'Sales')
                ->where('status_pembayaran', 'Paid')
                ->whereBetween('created_at', [$start, $end]);

            if ($range === 'today') {
                for ($i = 0; $i < 24; $i++) {
                    $chartData->put($i, ['label' => sprintf('%02d:00', $i), 'total' => 0]);
                }
                $sales = $query->select(DB::raw('HOUR(created_at) as hour'), DB::raw('SUM(total_akhir) as total'))
                    ->groupBy('hour')->get()->keyBy('hour');
                foreach ($sales as $h => $s) {
                    if ($chartData->has($h)) {
                        $chartData[$h] = ['label' => $chartData[$h]['label'], 'total' => (float)$s->total];
                    }
                }
            } elseif ($range === 'week' || $range === 'month') {
                $period = \Carbon\CarbonPeriod::create($start, $end);
                foreach ($period as $date) {
                    $key = $date->format('Y-m-d');
                    $chartData->put($key, ['label' => $date->translatedFormat('d M'), 'total' => 0]);
                }
                $sales = $query->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_akhir) as total'))
                    ->groupBy('date')->get()->keyBy('date');
                foreach ($sales as $d => $s) {
                    if ($chartData->has($d)) {
                        $chartData[$d] = ['label' => $chartData[$d]['label'], 'total' => (float)$s->total];
                    }
                }
            } else {
                for ($i = 5; $i >= 0; $i--) {
                    $m = \Carbon\Carbon::now()->subMonths($i);
                    $chartData->put($m->format('Y-m'), ['label' => $m->translatedFormat('M Y'), 'total' => 0]);
                }
                $sales = $query->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw("SUM(total_akhir) as total"))
                    ->groupBy('month')->get()->keyBy('month');
                foreach ($sales as $m => $s) {
                    if ($chartData->has($m)) {
                        $chartData[$m] = ['label' => $chartData[$m]['label'], 'total' => (float)$s->total];
                    }
                }
            }
            $data['charts']['monthlySales'] = $chartData->values();

            // 2. Real Stock Mutations (Activity Feed)
            $data['activityLog'] = StockMutation::with('product')
                ->where('office_id', $officeId)
                ->whereBetween('created_at', [$start, $end])
                ->latest()
                ->limit(15)
                ->get()
                ->map(function ($m) {
                    return [
                        'product_name' => $m->product->nama_produk ?? 'Produk',
                        'type' => $m->type, // in, out
                        'qty' => $m->qty,
                        'notes' => $m->notes,
                        'time' => $m->created_at->diffForHumans(),
                        'created_at' => $m->created_at->toISOString()
                    ];
                });

            // 3. Operational Bottlenecks (Alert Counts)
            $data['alerts'] = [
                'pending_approvals' => Invoice::where('office_id', $officeId)->where('status_dok', 'Waiting')->count(),
                'overdue_receivables' => Invoice::where('office_id', $officeId)->where('tipe_invoice', 'Sales')->where('status_pembayaran', '!=', 'Paid')->where('tgl_jatuh_tempo', '<', now())->count(),
                'zero_stock' => Product::where('office_id', $officeId)->where('track_stock', 1)->where('qty', '<=', 0)->count(),
            ];

            // 4. Existing Dashboard Stats (Consolidated)
            $data['stats']['total_pendapatan'] = Invoice::where('office_id', $officeId)
                ->where('tipe_invoice', 'Sales')
                ->where('status_pembayaran', 'Paid')
                ->whereBetween('created_at', [$start, $end])
                ->sum('total_akhir');

            $data['stats']['piutang_usaha'] = Invoice::where('office_id', $officeId)
                ->where('tipe_invoice', 'Sales')
                ->where('status_pembayaran', '!=', 'Paid')
                ->where('status_pembayaran', '!=', 'Draft')
                ->get()
                ->sum(function ($inv) {
                    return $inv->total_akhir - $inv->payment()->sum('jumlah_bayar');
                });

            // Utang Usaha (Total Purchase Debt) - NEW
            $data['stats']['utang_usaha'] = Invoice::where('office_id', $officeId)
                ->where('tipe_invoice', 'Purchase')
                ->where('status_pembayaran', '!=', 'Paid')
                ->where('status_pembayaran', '!=', 'Draft')
                ->get()
                ->sum(function ($inv) {
                    return $inv->total_akhir - $inv->payment()->sum('jumlah_bayar');
                });

            // Inventory Valuation (Total Asset Value)
            $data['stats']['inventory_value'] = Product::where('office_id', $officeId)
                ->get()
                ->sum(function ($p) {
                    return $p->qty * $p->harga_beli; // Basic valuation
                });

            $totalIncome = Payment::where('office_id', $officeId)
                ->whereHas('invoice', fn ($q) => $q->where('tipe_invoice', 'Sales'))
                ->sum('jumlah_bayar');
            $totalOutcome = Payment::where('office_id', $officeId)
                ->whereHas('invoice', fn ($q) => $q->where('tipe_invoice', 'Purchase'))
                ->sum('jumlah_bayar');
            $totalExpenses = Expense::where('office_id', $officeId)
                ->sum('jumlah');

            $data['stats']['saldo_aktif'] = $totalIncome - $totalOutcome - $totalExpenses;

            // --- PREVIOUS PERIOD STATS (For Comparison) ---
            $data['stats_prev']['total_pendapatan'] = Invoice::where('office_id', $officeId)
                ->where('tipe_invoice', 'Sales')
                ->where('status_pembayaran', 'Paid')
                ->whereBetween('created_at', [$start_prev, $end_prev_revenue])
                ->sum('total_akhir');

            $data['stats_prev']['piutang_usaha'] = Invoice::where('office_id', $officeId)
                ->where('tipe_invoice', 'Sales')
                ->where('status_pembayaran', '!=', 'Draft')
                ->where('created_at', '<=', $end_prev)
                ->get()->sum(function($inv) use ($end_prev) {
                    $paidUntilThen = $inv->payment()->where('tgl_pembayaran', '<=', $end_prev->toDateString())->sum('jumlah_bayar');
                    $debtAtThatTime = $inv->total_akhir - $paidUntilThen;
                    return $debtAtThatTime > 0 ? $debtAtThatTime : 0;
                });

            $data['stats_prev']['utang_usaha'] = Invoice::where('office_id', $officeId)
                ->where('tipe_invoice', 'Purchase')
                ->where('status_pembayaran', '!=', 'Draft')
                ->where('created_at', '<=', $end_prev)
                ->get()->sum(function($inv) use ($end_prev) {
                    $paidUntilThen = $inv->payment()->where('tgl_pembayaran', '<=', $end_prev->toDateString())->sum('jumlah_bayar');
                    $debtAtThatTime = $inv->total_akhir - $paidUntilThen;
                    return $debtAtThatTime > 0 ? $debtAtThatTime : 0;
                });

            $totalIncomePrev = Payment::where('office_id', $officeId)
                ->whereHas('invoice', fn ($q) => $q->where('tipe_invoice', 'Sales'))
                ->where('tgl_pembayaran', '<=', $end_prev->toDateString())
                ->sum('jumlah_bayar');
            $totalOutcomePrev = Payment::where('office_id', $officeId)
                ->whereHas('invoice', fn ($q) => $q->where('tipe_invoice', 'Purchase'))
                ->where('tgl_pembayaran', '<=', $end_prev->toDateString())
                ->sum('jumlah_bayar');
            $totalExpensesPrev = Expense::where('office_id', $officeId)
                ->where('tgl_biaya', '<=', $end_prev->toDateString())
                ->sum('jumlah');

            $data['stats_prev']['saldo_aktif'] = $totalIncomePrev - $totalOutcomePrev - $totalExpensesPrev;

            // Accurate Net Profit calculation based on GL
            $movements = DB::table('journal_details')
                ->join('journals', 'journal_details.journal_id', '=', 'journals.id')
                ->where('journals.office_id', $officeId)
                ->whereNull('journals.deleted_at')
                ->whereNull('journal_details.deleted_at')
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

            $data['stats']['pendapatan_bersih'] = $totalRevenue - $totalExpense;

            $data['stats']['new_orders'] = Invoice::where('office_id', $officeId)
                ->where('tipe_invoice', 'Sales')
                ->where('status_dok', 'Approved')
                ->whereDate('created_at', today())
                ->count();

            $data['stats']['low_stock'] = Product::where('office_id', $officeId)
                ->where('track_stock', 1)
                ->whereBetween('qty', [1, 10])
                ->count();

            $data['stats']['zero_stock'] = Product::where('office_id', $officeId)
                ->where('track_stock', 1)
                ->where('qty', '<=', 0)
                ->count();

            $recent = Invoice::with('mitra')
                ->where('office_id', $officeId)
                ->where('tipe_invoice', 'Sales')
                ->where('status_dok', 'Approved')
                ->whereBetween('created_at', [$start, $end])
                ->latest()
                ->limit(15)
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
            return response()->json(['success' => true, 'data' => $data]);
        } catch (Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
