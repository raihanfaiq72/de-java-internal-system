<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Office;

class DashboardController extends Controller
{
    public function index()
    {
        $officeId = session('active_office_id');
        $office = Office::find($officeId);

        $totalRevenue = Invoice::where('office_id', $officeId)
            ->where('status_pembayaran', 'Paid')
            ->sum('total_akhir');

        $newOrders = Invoice::where('office_id', $officeId)
            ->whereDate('created_at', today())
            ->count();

        $recentTransactions = Invoice::where('office_id', $officeId)
            ->latest()
            ->limit(5)
            ->get();

        // dd($recentTransactions);

        return view('Dashboard.index', compact(
            'office', 
            'totalRevenue', 
            'newOrders', 
            'recentTransactions'
        ));
    }
}