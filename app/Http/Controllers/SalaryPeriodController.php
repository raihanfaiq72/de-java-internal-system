<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\SalaryPeriod;
use App\Models\SalarySlip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalaryPeriodController extends Controller
{
    public function index()
    {
        $periods = SalaryPeriod::latest()->paginate(10);

        return view('Salary.periods.index', compact('periods'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'name' => 'nullable|string|max:255',
        ]);

        // Auto-generate name if empty
        if (! $request->name) {
            $start = \Carbon\Carbon::parse($request->start_date);
            $end = \Carbon\Carbon::parse($request->end_date);
            $request->merge(['name' => $start->format('d M').' - '.$end->format('d M Y')]);
        }

        SalaryPeriod::create($request->all());

        return redirect()->back()->with('success', 'Periode gaji berhasil dibuat');
    }

    public function show(SalaryPeriod $salaryPeriod)
    {
        $salaryPeriod->load(['salarySlips.employee']);
        $employees = Employee::where('status', 'active')->get();

        return view('Salary.periods.show', compact('salaryPeriod', 'employees'));
    }

    public function storeBatch(Request $request, SalaryPeriod $salaryPeriod)
    {
        if ($salaryPeriod->status === 'closed') {
            return redirect()->back()->with('error', 'Periode ini sudah ditutup.');
        }
        $data = $request->validate([
            'slips' => 'required|array',
            'slips.*.employee_id' => 'required|integer|exists:employees,id',
            'slips.*.work_days' => 'nullable|integer|min:0',
            'slips.*.daily_basic_salary' => 'nullable|numeric|min:0',
            'slips.*.basic_salary' => 'nullable|numeric|min:0',
            'slips.*.premi' => 'nullable|numeric|min:0',
            'slips.*.meal_allowance' => 'nullable|numeric|min:0',
            'slips.*.late_deduction' => 'nullable|numeric|min:0',
            'slips.*.other_deduction' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            foreach ($data['slips'] as $input) {
                $basicSalary = ($input['daily_basic_salary'] ?? 0) * ($input['work_days'] ?? 0);
                if (isset($input['basic_salary'])) {
                    $basicSalary = (float) $input['basic_salary'];
                }
                $premi = $input['premi'] ?? 0;
                $mealAllowance = $input['meal_allowance'] ?? 0;
                $lateDeduction = $input['late_deduction'] ?? 0;
                $otherDeduction = $input['other_deduction'] ?? 0;
                $totalSalary = $basicSalary + $premi + $mealAllowance - $lateDeduction - $otherDeduction;

                SalarySlip::updateOrCreate(
                    [
                        'salary_period_id' => $salaryPeriod->id,
                        'employee_id' => $input['employee_id'],
                    ],
                    [
                        'basic_salary' => $basicSalary,
                        'premi' => $premi,
                        'meal_allowance' => $mealAllowance,
                        'late_deduction' => $lateDeduction,
                        'other_deduction' => $otherDeduction,
                        'total_salary' => $totalSalary,
                        'status' => 'draft',
                        'details' => [
                            'work_days' => $input['work_days'] ?? null,
                            'daily_rate' => $input['daily_basic_salary'] ?? null,
                        ],
                    ],
                );
            }
            DB::commit();

            return redirect()->back()->with('success', 'Data gaji karyawan berhasil disimpan.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Gagal menyimpan data: '.$e->getMessage());
        }
    }

    public function storeOne(Request $request, SalaryPeriod $salaryPeriod)
    {
        if ($salaryPeriod->status === 'closed') {
            return redirect()->back()->with('error', 'Periode ini sudah ditutup.');
        }
        $input = $request->validate([
            'employee_id' => 'required|integer|exists:employees,id',
            'work_days' => 'required|integer|min:0',
            'daily_basic_salary' => 'required|numeric|min:0',
            'premi' => 'nullable|numeric|min:0',
            'meal_allowance' => 'nullable|numeric|min:0',
            'late_deduction' => 'nullable|numeric|min:0',
            'other_deduction' => 'nullable|numeric|min:0',
        ]);

        $basic = (float) $input['daily_basic_salary'] * (int) $input['work_days'];
        $premi = (float) ($input['premi'] ?? 0);
        $meal = (float) ($input['meal_allowance'] ?? 0);
        $late = (float) ($input['late_deduction'] ?? 0);
        $other = (float) ($input['other_deduction'] ?? 0);
        $total = $basic + $premi + $meal - $late - $other;

        SalarySlip::updateOrCreate(
            [
                'salary_period_id' => $salaryPeriod->id,
                'employee_id' => $input['employee_id'],
            ],
            [
                'basic_salary' => $basic,
                'premi' => $premi,
                'meal_allowance' => $meal,
                'late_deduction' => $late,
                'other_deduction' => $other,
                'total_salary' => $total,
                'status' => 'draft',
                'details' => [
                    'work_days' => $input['work_days'],
                    'daily_rate' => $input['daily_basic_salary'],
                ],
            ],
        );

        return redirect()->back()->with('success', 'Slip gaji karyawan disimpan.');
    }

    public function destroy(SalaryPeriod $salaryPeriod)
    {
        if ($salaryPeriod->salarySlips()->where('status', 'paid')->exists()) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus periode yang sudah ada pembayaran.');
        }
        $salaryPeriod->delete();

        return redirect()->back()->with('success', 'Periode gaji dihapus.');
    }
}
