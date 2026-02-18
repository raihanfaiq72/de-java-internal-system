<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Services\JournalService;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    use LogsActivity;

    protected $journalService;

    public function __construct(JournalService $journalService)
    {
        $this->journalService = $journalService;
    }

    public function index(Request $request)
    {
        $query = Expense::with(['akunKeuangan', 'akunBeban'])->where('office_id', session('active_office_id'));

        if ($request->tgl_mulai && $request->tgl_selesai) {
            $query->whereBetween('tgl_biaya', [$request->tgl_mulai, $request->tgl_selesai]);
        }

        $data = $query->latest()->paginate(10);

        return apiResponse(true, 'Data biaya operasional', $data);
    }

    public function show($id)
    {
        $data = Expense::with(['akunKeuangan', 'akunBeban'])
            ->where('office_id', session('active_office_id'))
            ->find($id);

        if (! $data) {
            return apiResponse(false, 'Biaya tidak ditemukan', null, null, 404);
        }

        return apiResponse(true, 'Detail biaya', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_biaya' => 'required',
            'tgl_biaya' => 'required|date',
            'jumlah' => 'required|numeric|min:0.01',
            'akun_keuangan_id' => 'required|exists:financial_accounts,id',
            'akun_beban_id' => 'required|exists:chart_of_accounts,id',
            'lampiran' => 'nullable|file|max:10240', // Max 10MB
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        if (! session()->has('active_office_id')) {
            return apiResponse(false, 'Silakan pilih outlet terlebih dahulu.', null, null, 422);
        }

        $input = $request->all();
        $input['office_id'] = session('active_office_id');

        // Handle dynamic category creation
        if ($request->filled('kategori_biaya')) {
            $categoryName = $request->kategori_biaya;
            // Check if exists
            $exists = ExpenseCategory::where('office_id', session('active_office_id'))
                ->where('name', $categoryName)
                ->exists();
            if (! $exists) {
                ExpenseCategory::create([
                    'office_id' => session('active_office_id'),
                    'name' => $categoryName,
                ]);
            }
        }

        // Handle File Upload
        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $path = $file->store('expenses', 'public');
            $input['lampiran'] = $path;
        }

        $data = Expense::create($input);

        $this->logActivity('Create', 'expenses', $data->id, null, $data);

        // Automatic Journal Entry
        $this->journalService->recordExpense($data);

        return apiResponse(true, 'Biaya berhasil dicatat', $data, null, 201);
    }

    public function update(Request $request, $id)
    {
        $data = Expense::where('office_id', session('active_office_id'))->find($id);
        if (! $data) {
            return apiResponse(false, 'Biaya tidak ditemukan', null, null, 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_biaya' => 'required',
            'tgl_biaya' => 'required|date',
            'jumlah' => 'required|numeric|min:0.01',
            'akun_keuangan_id' => 'required|exists:chart_of_accounts,id',
            'akun_beban_id' => 'required|exists:chart_of_accounts,id',
            'lampiran' => 'nullable|file|max:10240',
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validasi gagal', null, $validator->errors(), 422);
        }

        // Handle dynamic category creation
        if ($request->filled('kategori_biaya')) {
            $categoryName = $request->kategori_biaya;
            // Check if exists
            $exists = ExpenseCategory::where('office_id', session('active_office_id'))
                ->where('name', $categoryName)
                ->exists();
            if (! $exists) {
                ExpenseCategory::create([
                    'office_id' => session('active_office_id'),
                    'name' => $categoryName,
                ]);
            }
        }

        $before = $data->toArray();
        $input = $request->all();

        // Handle File Upload
        if ($request->hasFile('lampiran')) {
            // Delete old file
            if ($data->lampiran && Storage::disk('public')->exists($data->lampiran)) {
                Storage::disk('public')->delete($data->lampiran);
            }

            $file = $request->file('lampiran');
            $path = $file->store('expenses', 'public');
            $input['lampiran'] = $path;
        }

        $data->update($input);

        $this->logActivity('Update', 'expenses', $id, $before, $data);

        return apiResponse(true, 'Biaya berhasil diperbarui', $data);
    }

    public function destroy($id)
    {
        $data = Expense::where('office_id', session('active_office_id'))->find($id);
        if (! $data) {
            return apiResponse(false, 'Biaya tidak ditemukan', null, null, 404);
        }

        $before = $data->toArray();

        // Delete file if exists
        if ($data->lampiran && Storage::disk('public')->exists($data->lampiran)) {
            Storage::disk('public')->delete($data->lampiran);
        }

        $data->delete();

        $this->logActivity('Delete', 'expenses', $id, $before, null);

        return apiResponse(true, 'Biaya berhasil dihapus');
    }

    public function analyticsSummary(Request $request)
    {
        $officeId = session('active_office_id');
        $query = Expense::where('office_id', $officeId);

        if ($request->tgl_mulai && $request->tgl_selesai) {
            $query->whereBetween('tgl_biaya', [$request->tgl_mulai, $request->tgl_selesai]);
        }

        if ($request->kategori_biaya) {
            $query->where('kategori_biaya', $request->kategori_biaya);
        }

        $expenses = $query->get();

        // Data for Pie/Donut Chart (Group by Category)
        $byCategory = $expenses->groupBy('kategori_biaya')->map(function ($row) {
            return $row->sum('jumlah');
        });

        // Data for Bar Chart (Total Amount - if single category filtered, shows total, else shows comparison)
        // Let's make Bar chart same as Pie chart data for now (comparison between categories),
        // or if specific requirement "Jumlah Biaya" means something else.
        // Based on image "Jumlah Biaya" looks like a single bar when filtered by "mimik".
        // If no filter, maybe top categories.
        // Let's stick to By Category for both, just different visualization.

        return apiResponse(true, 'Data analitik summary', [
            'labels' => $byCategory->keys(),
            'series' => $byCategory->values(),
            'total' => $expenses->sum('jumlah'),
        ]);
    }

    public function analyticsTrend(Request $request)
    {
        $officeId = session('active_office_id');
        $year = $request->year ?? date('Y');

        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[] = Expense::where('office_id', $officeId)
                ->whereYear('tgl_biaya', $year)
                ->whereMonth('tgl_biaya', $i)
                ->sum('jumlah');
        }

        return apiResponse(true, 'Data tren biaya', [
            'categories' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            'series' => [
                [
                    'name' => 'Total Biaya',
                    'data' => $monthlyData,
                ],
            ],
        ]);
    }
}
