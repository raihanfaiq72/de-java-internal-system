<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Office;

class DashboardController extends Controller
{
    public function index()
    {
        $officeId = session('active_office_id');
        $office = Office::find($officeId);

        $totalRevenue = DB::table('invoices')
            ->where('office_id', $officeId)
            ->where('status_pembayaran', 'Paid')
            ->sum('total_akhir');

        $newOrders = DB::table('invoices')
            ->where('office_id', $officeId)
            ->whereDate('created_at', today())
            ->count();

        $recentTransactions = DB::table('invoices')
            ->where('office_id', $officeId)
            ->latest()
            ->limit(5)
            ->get();

        return view('Dashboard.index', compact(
            'office', 
            'totalRevenue', 
            'newOrders', 
            'recentTransactions'
        ));
    }
}