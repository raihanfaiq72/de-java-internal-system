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
            'kelompok_akun_id' => 'required|exists:kelompok_akuns,id',
            'tipe_akun_id' => 'required|exists:tipe_akuns,id',
            'is_kas_bank' => 'boolean'
        ]);

        $coa = COA::create([
            'office_id' => 1,
            'kode_akun' => $request->kode_akun,
            'nama_akun' => $request->nama_akun,
            'kelompok_akun_id' => $request->kelompok_akun_id,
            'tipe_akun_id' => $request->tipe_akun_id,
            'is_kas_bank' => $request->is_kas_bank ?? 0
        ]);

        return response()->json(['message' => 'COA berhasil ditambahkan', 'data' => $coa]);
    }

    public function update(Request $request, $id)
    {
        $coa = COA::findOrFail($id);

        $request->validate([
            'kode_akun' => 'required|unique:chart_of_accounts,kode_akun,' . $id,
            'nama_akun' => 'required',
            'kelompok_akun_id' => 'required|exists:kelompok_akuns,id',
            'tipe_akun_id' => 'required|exists:tipe_akuns,id',
            'is_kas_bank' => 'boolean'
        ]);

        $coa->update($request->all());

        return response()->json(['message' => 'COA berhasil diupdate']);
    }

    public function destroy($id)
    {
        COA::findOrFail($id)->delete();
        return response()->json(['message' => 'COA berhasil dihapus']);
    }
}