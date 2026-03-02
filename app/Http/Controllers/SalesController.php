<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    private $views = 'Sales.';

    public function index()
    {
        $users = \App\Models\User::select('id', 'name')->get();
        // Check if current user is superadmin (role id 1) or has permission
        $user = auth()->user();
        // Assuming role_id 1 is Super Admin based on standard seeding, or check relationship
        // Logic: if role name 'superadmin' -> can select. Else fixed to self.
        // Let's pass the flag to view
        $canSelectSales = false;
        
        // Ensure roles relationship is loaded or handle null
        // The error "Call to a member function contains() on null" means $user->roles is null.
        // This happens if roles relationship is not eager loaded or user model doesn't have roles property set correctly as a collection.
        // If using standard Laravel relationship, it should return Collection or null if not loaded but usually it returns empty collection.
        // Unless roles is not defined in User model or it's returning null.
        
        // Safest check:
        if ($user && $user->id == 1) {
             $canSelectSales = true;
        } elseif ($user && $user->roles && $user->roles->contains('name', 'superadmin')) {
             $canSelectSales = true;
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

    public function show($id)
    {
        return view($this->views.'detail', compact('id'));
    }

    public function receipt()
    {
        return view($this->views.'receipt');
    }

    public function showReceipt($id)
    {
        return view($this->views.'receipt-detail', compact('id'));
    }

    public function printInvoice($id)
    {
        return view($this->views.'Nota.SalesNota', compact('id'));
    }

    public function printReceipt($id)
    {
        return view($this->views.'Nota.ReceiptNota', compact('id'));
    }
}
