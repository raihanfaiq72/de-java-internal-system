<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;

class MitraController extends Controller
{
    private $views = 'Mitra.';

    public function index()
    {
        return view($this->views.'index');
    }

    public function export(Request $request)
    {
        $query = Partner::with(['akunHutang', 'akunPiutang'])
            ->where('office_id', session('active_office_id'));

        if ($request->has('trashed') && $request->trashed == '1') {
            $query->onlyTrashed();
        }

        if ($request->filled('search')) {
            $value = $request->search;
            $query->where(function ($q) use ($value) {
                $q->where('nama', 'LIKE', "%{$value}%")
                    ->orWhere('nomor_mitra', 'LIKE', "%{$value}%")
                    ->orWhere('email', 'LIKE', "%{$value}%")
                    ->orWhere('no_hp', 'LIKE', "%{$value}%");
            });
        }

        if ($request->filled('tipe_mitra')) {
            $query->where('tipe_mitra', $request->tipe_mitra);
        }

        $mitras = $query->latest()->get();

        return view($this->views.'export', compact('mitras'));
    }
}
