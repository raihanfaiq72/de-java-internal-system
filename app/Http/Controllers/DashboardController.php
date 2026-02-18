<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrderFleet;
use App\Models\Invoice;
use App\Models\Office;
use App\Models\Product;
use App\Models\UserOfficeRole;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $officeId = session('active_office_id');
        $office = Office::find($officeId);
        $user = Auth::user();

        // Get User Role in Current Office
        $userRoleRel = UserOfficeRole::with('role')
            ->where('user_id', $user->id)
            ->where('office_id', $officeId)
            ->first();

        $roleName = $userRoleRel && $userRoleRel->role ? $userRoleRel->role->name : 'User';

        // Base Data
        $data = [
            'office' => $office,
            'roleName' => $roleName,
            'isDriver' => false,
            'stats' => [],
            'recentTransactions' => [],
            'driverJobs' => [],
        ];

        // Logic Based on Role
        // Check if Driver
        if (stripos($roleName, 'Driver') !== false || stripos($roleName, 'Kurir') !== false) {
            $data['isDriver'] = true;

            // Driver Stats
            $data['driverJobs'] = DeliveryOrderFleet::with(['deliveryOrder'])
                ->where('driver_id', $user->id)
                ->whereIn('status', ['assigned', 'in_transit'])
                ->orderBy('created_at', 'desc')
                ->get();

            $data['stats']['completed_today'] = DeliveryOrderFleet::where('driver_id', $user->id)
                ->where('status', 'completed')
                ->whereDate('updated_at', today())
                ->count();

            $data['stats']['active_jobs'] = $data['driverJobs']->count();

        } else {
            // Admin / Staff Logic

            // 1. Sales Stats
            $data['stats']['revenue'] = Invoice::where('office_id', $officeId)
                ->where('tipe_invoice', 'Sales')
                ->where('status_pembayaran', 'Paid')
                ->sum('total_akhir');

            $data['stats']['new_orders'] = Invoice::where('office_id', $officeId)
                ->where('tipe_invoice', 'Sales')
                ->whereDate('created_at', today())
                ->count();

            // 2. Purchase Stats
            $data['stats']['pending_purchases'] = Invoice::where('office_id', $officeId)
                ->where('tipe_invoice', 'Purchase')
                ->where('status_pembayaran', '!=', 'Paid')
                ->count();

            // 3. Stock Stats
            $data['stats']['low_stock'] = Product::where('office_id', $officeId)
                ->where('track_stock', 1)
                ->where('qty', '<=', 10) // Low stock threshold
                ->count();

            // Recent Sales Transactions
            $data['recentTransactions'] = Invoice::with('mitra')
                ->where('office_id', $officeId)
                ->where('tipe_invoice', 'Sales')
                ->latest()
                ->limit(5)
                ->get();
        }

        return view('Dashboard.index', $data);
    }
}
