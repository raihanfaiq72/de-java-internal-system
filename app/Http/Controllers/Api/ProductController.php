<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
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
        $query = Product::with(['category', 'supplier', 'brand'])
            ->where('office_id', session('active_office_id'));

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_produk', 'LIKE', "%{$request->search}%")
                    ->orWhere('sku_kode', 'LIKE', "%{$request->search}%");
            });
        }

        if ($request->category) {
            $query->where('product_category_id', $request->category);
        }

        if ($request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $perPage = $request->get('per_page', 10);
        if ($perPage >= 1000) {
            $data = $query->latest()->get();
        } else {
            $data = $query->latest()->paginate($perPage)->withQueryString();
        }

        return apiResponse(true, 'Data produk', $data);
    }

    public function show($id)
    {
        $data = Product::with(['category', 'supplier', 'brand'])
            ->where('office_id', session('active_office_id'))
            ->find($id);
        if (! $data) {
            return apiResponse(false, 'Produk tidak ditemukan', null, null, 404);
        }

        return apiResponse(true, 'Detail produk', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_produk' => 'required|string|max:255',
            'sku_kode' => 'nullable|string|max:100|unique:products,sku_kode,NULL,id,office_id,' . session('active_office_id'),
            'product_category_id' => 'nullable|exists:product_categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit_id' => 'nullable|exists:unit_ukurans,id',
            'coa_id' => 'nullable|exists:coas,id',
            'kemasan' => 'nullable|string|max:100',
            'harga_beli' => 'nullable|numeric|min:0',
            'harga_jual' => 'nullable|numeric|min:0',
            'harga_tempo' => 'nullable|numeric|min:0',
            'kemasan.integer' => 'Kemasan harus berupa angka.',
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        if (! session()->has('active_office_id')) {
            return apiResponse(false, 'Silakan pilih outlet terlebih dahulu.', null, null, 422);
        }

        $input = $request->all();
        $input['office_id'] = session('active_office_id');

        try {
            $data = Product::create($input);
        } catch (\Throwable $e) {
            return apiResponse(false, 'Gagal menyimpan produk. Periksa data Brand, Supplier, Kategori, dan COA.', null, null, 422);
        }

        return apiResponse(true, 'Produk berhasil ditambahkan', $data, null, 201);
    }

    public function update(Request $request, $id)
    {
        $data = Product::where('office_id', session('active_office_id'))->find($id);
        if (! $data) {
            return apiResponse(false, 'Produk tidak ditemukan', null, null, 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_produk' => 'required',
            'harga_beli' => 'nullable|numeric|min:0',
            'harga_jual' => 'nullable|numeric|min:0',
            'harga_tempo' => 'nullable|numeric|min:0',
            'supplier_id' => 'required|exists:mitras,id',
            'brand_id' => 'required|exists:brands,id',
            'product_category_id' => 'required|exists:product_categories,id',
            'coa_id' => 'required|exists:chart_of_accounts,id',
            'kemasan' => 'nullable|integer|min:1',
            'satuan' => 'nullable|string|max:20',
        ], [
            'nama_produk.required' => 'Nama produk wajib diisi.',
            'supplier_id.required' => 'Supplier wajib dipilih.',
            'supplier_id.exists' => 'Supplier tidak valid.',
            'brand_id.required' => 'Brand wajib dipilih.',
            'brand_id.exists' => 'Brand tidak valid.',
            'product_category_id.required' => 'Kategori wajib dipilih.',
            'product_category_id.exists' => 'Kategori tidak valid.',
            'coa_id.required' => 'COA wajib dipilih.',
            'coa_id.exists' => 'COA tidak valid.',
            'kemasan.integer' => 'Kemasan harus berupa angka.',
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        $before = $data->toArray();
        try {
            $data->update($request->all());
        } catch (\Throwable $e) {
            return apiResponse(false, 'Gagal menyimpan perubahan produk. Periksa data Brand, Supplier, Kategori, dan COA.', null, null, 422);
        }

        return apiResponse(true, 'Produk berhasil diperbarui', $data);
    }

    public function destroy($id)
    {
        $data = Product::where('office_id', session('active_office_id'))->find($id);
        if (! $data) {
            return apiResponse(false, 'Produk tidak ditemukan', null, null, 404);
        }

        $before = $data->toArray();
        $data->delete();

        return apiResponse(true, 'Produk berhasil dihapus');
    }

    public function search(Request $request, $value)
    {
        $data = Product::where('office_id', session('active_office_id'))
            ->where(function ($q) use ($value) {
                $q->where('nama_produk', 'LIKE', "%$value%")
                    ->orWhere('sku_kode', 'LIKE', "%$value%");
            })
            ->latest()
            ->paginate($request->get('per_page', 10))
            ->withQueryString();

        return apiResponse(true, 'Hasil pencarian produk', $data);
    }

    public function nextSku()
    {
        $last = Product::withTrashed()
            // ->where('office_id', session('active_office_id'))
            ->orderBy('id', 'desc')->first();

        if (! $last || ! $last->sku_kode) {
            return apiResponse(true, 'Next SKU', 'PROD-00001');
        }

        preg_match('/(\d+)$/', $last->sku_kode, $matches);
        $number = isset($matches[1]) ? (int) $matches[1] + 1 : 1;

        $sku = 'PROD-'.str_pad($number, 5, '0', STR_PAD_LEFT);

        return apiResponse(true, 'Next SKU', $sku);
    }

    public function recalculateStock($id)
    {
        $product = Product::where('office_id', session('active_office_id'))->find($id);
        if (! $product) {
            return apiResponse(false, 'Produk tidak ditemukan', null, null, 404);
        }

        $newQty = $this->stockService->recalculateProductStock($id);

        return apiResponse(true, 'Stok berhasil direkalkulasi', ['qty' => $newQty]);
    }

    public function bulkStore(Request $request)
    {
        $products = $request->input('products', []);
        
        if (empty($products)) {
            return apiResponse(false, 'Tidak ada produk untuk disimpan', null, null, 422);
        }

        $validator = Validator::make(['products' => $products], [
            'products.*.nama_produk' => 'required|string|max:255',
            'products.*.sku_kode' => 'nullable|string|max:100|unique:products,sku_kode,NULL,id,office_id,' . session('active_office_id'),
            'products.*.product_category_id' => 'nullable|exists:product_categories,id',
            'products.*.supplier_id' => 'nullable|exists:suppliers,id',
            'products.*.brand_id' => 'nullable|exists:brands,id',
            'products.*.unit_id' => 'nullable|exists:unit_ukurans,id',
            'products.*.coa_id' => 'nullable|exists:coas,id',
            'products.*.kemasan' => 'nullable|string|max:100',
            'products.*.qty' => 'nullable|integer|min:0',
            'products.*.harga_beli' => 'nullable|numeric|min:0',
            'products.*.harga_jual' => 'nullable|numeric|min:0',
            'products.*.harga_tempo' => 'nullable|numeric|min:0',
            'products.*.kemasan.integer' => 'Kemasan harus berupa angka.',
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        if (!session()->has('active_office_id')) {
            return apiResponse(false, 'Silakan pilih outlet terlebih dahulu.', null, null, 422);
        }

        $createdProducts = [];
        $errors = [];

        foreach ($products as $index => $productData) {
            try {
                $productData = array_merge($productData, [
                    'office_id' => session('active_office_id'),
                    'track_stock' => 1,
                    'akun_penjualan_id' => 1,
                    'akun_pembelian_id' => 1,
                ]);

                $product = Product::create($productData);
                $createdProducts[] = $product;
            } catch (\Throwable $e) {
                $errors[] = "Baris ke-" . ($index + 1) . ": " . $e->getMessage();
            }
        }

        if (!empty($errors)) {
            return apiResponse(false, 'Beberapa produk gagal disimpan', null, $errors, 422);
        }

        return apiResponse(true, 'Berhasil menyimpan ' . count($createdProducts) . ' produk', $createdProducts, null, 201);
    }
}
