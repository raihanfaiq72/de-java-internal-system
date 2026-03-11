<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    private $views = 'Sales.';

    public function index()
    {
        $users = \App\Models\User::select('id', 'name')->get();
        $user = auth()->user();

        $canSelectSales = false;
        if ($user && $user->id == 1) {
            $canSelectSales = true;
        } elseif ($user && method_exists($user, 'roles')) {
            $canSelectSales = $user->roles()->where('name', 'superadmin')->exists();
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
