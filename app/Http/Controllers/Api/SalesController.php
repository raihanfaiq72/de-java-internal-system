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
}
