<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryOrderFleet;
use App\Models\Invoice;
use App\Models\Office;
use App\Models\Product;
use App\Models\UserOfficeRole;
use Illuminate\Support\Facades\Auth;
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
                $data['stats']['revenue'] = Invoice::where('office_id', $officeId)
                    ->where('tipe_invoice', 'Sales')
                    ->where('status_pembayaran', 'Paid')
                    ->sum('total_akhir');

                $data['stats']['new_orders'] = Invoice::where('office_id', $officeId)
                    ->where('tipe_invoice', 'Sales')
                    ->whereDate('created_at', today())
                    ->count();

                $data['stats']['pending_purchases'] = Invoice::where('office_id', $officeId)
                    ->where('tipe_invoice', 'Purchase')
                    ->where('status_pembayaran', '!=', 'Paid')
                    ->count();

                $data['stats']['low_stock'] = Product::where('office_id', $officeId)
                    ->where('track_stock', 1)
                    ->where('qty', '<=', 10)
                    ->count();

                $recent = Invoice::with('mitra')
                    ->where('office_id', $officeId)
                    ->where('tipe_invoice', 'Sales')
                    ->latest()
                    ->limit(5)
                    ->get();

                $data['recentTransactions'] = $recent->map(function ($tx) {
                    return [
                        'nomor_invoice' => $tx->nomor_invoice,
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

