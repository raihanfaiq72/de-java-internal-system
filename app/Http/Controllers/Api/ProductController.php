<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ActivityLog;
use App\Models\ProductCategorie;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index(Request $request)
    {
        $query = Product::with(['category', 'unit'])
            ->where('office_id', session('active_office_id'));

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_produk', 'LIKE', "%{$request->search}%")
                ->orWhere('sku_kode', 'LIKE', "%{$request->search}%");
            });
        }

        if ($request->category) {
            $query->where('category_id', $request->category);
        }

        $data = $query->latest()->paginate(10);

        return apiResponse(true, 'Data produk', $data);
    }

    public function show($id)
    {
        $data = Product::with(['category', 'unit'])
            ->where('office_id', session('active_office_id'))
            ->find($id);
        if (!$data) {
            return apiResponse(false, 'Produk tidak ditemukan', null, null, 404);
        }
        return apiResponse(true, 'Detail produk', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sku_kode' => 'required|unique:products,sku_kode',
            'nama_produk' => 'required',
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        if (!session()->has('active_office_id')) {
            return apiResponse(false, 'Silakan pilih outlet terlebih dahulu.', null, null, 422);
        }

        $input = $request->all();
        $input['office_id'] = session('active_office_id');

        $data = Product::create($input);

        $this->logActivity('Create', 'products', $data->id, null, $data);

        return apiResponse(true, 'Produk berhasil ditambahkan', $data, null, 201);
    }

    public function update(Request $request, $id)
    {
        $data = Product::where('office_id', session('active_office_id'))->find($id);
        if (!$data) {
            return apiResponse(false, 'Produk tidak ditemukan', null, null, 404);
        }

        $validator = Validator::make($request->all(), [
            'sku_kode' => 'required|unique:products,sku_kode,' . $id,
            'nama_produk' => 'required',
            'harga_beli' => 'nullable|numeric|min:0',
            'harga_jual' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        $before = $data->toArray();
        $data->update($request->all());

        $this->logActivity('Update', 'products', $id, $before, $data);

        return apiResponse(true, 'Produk berhasil diperbarui', $data);
    }

    public function destroy($id)
    {
        $data = Product::where('office_id', session('active_office_id'))->find($id);
        if (!$data) {
            return apiResponse(false, 'Produk tidak ditemukan', null, null, 404);
        }

        $before = $data->toArray();
        $data->delete();

        $this->logActivity('Soft Delete', 'products', $id, $before, null);

        return apiResponse(true, 'Produk berhasil dihapus');
    }

    public function search($value)
    {
        $data = Product::where('office_id', session('active_office_id'))
            ->where(function($q) use ($value) {
                $q->where('nama_produk', 'LIKE', "%$value%")
                  ->orWhere('sku_kode', 'LIKE', "%$value%");
            })
            ->paginate(10);

        return apiResponse(true, 'Hasil pencarian produk', $data);
    }

    public function nextSku()
    {
        $last = Product::where('office_id', session('active_office_id'))
            ->orderBy('id', 'desc')->first();

        if (!$last || !$last->sku_kode) {
            return apiResponse(true, 'Next SKU', 'PROD-00001');
        }

        preg_match('/(\d+)$/', $last->sku_kode, $matches);
        $number = isset($matches[1]) ? (int)$matches[1] + 1 : 1;

        $sku = 'PROD-' . str_pad($number, 5, '0', STR_PAD_LEFT);

        return apiResponse(true, 'Next SKU', $sku);
    }

    public function recalculateStock($id)
    {
        $product = Product::where('office_id', session('active_office_id'))->find($id);
        if (!$product) {
            return apiResponse(false, 'Produk tidak ditemukan', null, null, 404);
        }

        $newQty = $this->stockService->recalculateProductStock($id);

        return apiResponse(true, 'Stok berhasil direkalkulasi', ['qty' => $newQty]);
    }

    private function logActivity($tindakan, $tabel, $dataId, $before, $after)
    {
        ActivityLog::create([
            'office_id' => session('active_office_id'),
            'user_id' => 1,
            'tindakan' => $tindakan,
            'tabel_terkait' => $tabel,
            'data_id' => $dataId,
            'data_sebelum' => $before,
            'data_sesudah' => $after,
            'ip_address' => request()->ip()
        ]);
    }
}