<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\COA;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartOfAccountController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('chart_of_accounts');

        if ($request->has('is_kas_bank')) {
            $query->where('is_kas_bank', $request->is_kas_bank);
        }

        $data = $query->get();

        return apiResponse(true, 'Data Chart of Accounts', $data);
    }

    public function show($id)
    {
        return COA::findOrFail($id);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_akun' => 'required|unique:chart_of_accounts,kode_akun',
            'nama_akun' => 'required',
            'kelompok_akun_id' => 'required|exists:coa_group,id',
            'tipe_akun_id' => 'required|exists:coa_type,id',
            'is_kas_bank' => 'boolean',
        ]);

        $coa = COA::create([
            'office_id' => session('active_office_id') ?? 1,
            'kode_akun' => $request->kode_akun,
            'nama_akun' => $request->nama_akun,
            'tipe_id' => $request->tipe_akun_id,
            'is_kas_bank' => $request->is_kas_bank ?? 0,
        ]);

        return response()->json(['message' => 'COA berhasil ditambahkan', 'data' => $coa]);
    }

    public function update(Request $request, $id)
    {
        $coa = COA::findOrFail($id);

        $request->validate([
            'kode_akun' => 'required|unique:chart_of_accounts,kode_akun,'.$id,
            'nama_akun' => 'required',
            'kelompok_akun_id' => 'required|exists:coa_group,id',
            'tipe_akun_id' => 'required|exists:coa_type,id',
            'is_kas_bank' => 'boolean',
        ]);

        // Check if modifying critical fields when data exists
        $hasData = \App\Models\JournalDetail::where('akun_id', $id)->exists();

        if ($hasData) {
            if ($coa->kode_akun != $request->kode_akun) {
                return response()->json(['message' => 'Tidak bisa mengubah Kode Akun karena sudah ada transaksi.'], 422);
            }
            if ($coa->tipe_id != $request->tipe_akun_id) {
                return response()->json(['message' => 'Tidak bisa mengubah Tipe Akun karena sudah ada transaksi.'], 422);
            }
        }

        $coa->update([
            'kode_akun' => $request->kode_akun,
            'nama_akun' => $request->nama_akun,
            'tipe_id' => $request->tipe_akun_id,
            'is_kas_bank' => $request->is_kas_bank ?? 0,
        ]);

        return response()->json(['message' => 'COA berhasil diupdate']);
    }

    public function destroy($id)
    {
        $coa = COA::findOrFail($id);

        // Validation: Check if account has transactions
        $hasTransactions = \App\Models\JournalDetail::where('akun_id', $id)->exists();
        if ($hasTransactions) {
            return response()->json(['message' => 'Gagal menghapus: Akun ini memiliki riwayat transaksi.'], 422);
        }

        // Check expenses just in case (though they should have journal entries)
        $hasExpenses = \DB::table('expenses')->where('akun_beban_id', $id)->exists();
        if ($hasExpenses) {
            return response()->json(['message' => 'Gagal menghapus: Akun ini digunakan dalam data pengeluaran.'], 422);
        }

        $coa->delete();

        return response()->json(['message' => 'COA berhasil dihapus']);
    }
}
