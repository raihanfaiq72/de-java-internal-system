<?php

namespace App\Http\Controllers;

use App\Models\SalaryPeriod;
use App\Models\SalarySlip;
use App\Models\Employee;
use App\Models\Attendance;
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
        if (!$request->name) {
            $start = \Carbon\Carbon::parse($request->start_date);
            $end = \Carbon\Carbon::parse($request->end_date);
            $request->merge(['name' => $start->format('d M') . ' - ' . $end->format('d M Y')]);
        }

        SalaryPeriod::create($request->all());

        return redirect()->back()->with('success', 'Periode gaji berhasil dibuat');
    }

    public function show(SalaryPeriod $salaryPeriod)
    {
        $salaryPeriod->load(['salarySlips.employee']);
        return view('Salary.periods.show', compact('salaryPeriod'));
    }

    public function generate(SalaryPeriod $salaryPeriod)
    {
        if ($salaryPeriod->status === 'closed') {
            return redirect()->back()->with('error', 'Periode ini sudah ditutup.');
        }

        // Logic to generate salary slips
        // 1. Get all active employees
        $employees = Employee::where('status', 'active')->get();

        DB::beginTransaction();
        try {
            foreach ($employees as $employee) {
                // Check if slip already exists
                $exists = SalarySlip::where('salary_period_id', $salaryPeriod->id)
                    ->where('employee_id', $employee->id)
                    ->exists();
                
                if ($exists) continue;

                // Calculate attendance
                $attendances = Attendance::where('employee_id', $employee->id)
                    ->whereBetween('date', [$salaryPeriod->start_date, $salaryPeriod->end_date])
                    ->get();

                $presentCount = $attendances->where('status', 'present')->count();
                $lateCount = $attendances->where('status', 'late')->count(); // Assuming late is also paid or handled
                // Simple logic: Pay for present + late
                $paidDays = $presentCount + $lateCount;

                $basicSalary = $paidDays * $employee->daily_salary;
                $premi = $employee->premi; // Flat premi per period? Or daily? Assuming flat for now based on migration default
                
                // Meal allowance logic (example: if present)
                // Let's assume meal allowance is part of daily salary or separate?
                // Migration has 'meal_allowance' column. Let's assume 0 for now or add to employee model.
                $mealAllowance = 0; 

                // Deductions
                $lateDeduction = 0; // Logic for late deduction
                $otherDeduction = 0;

                $totalSalary = $basicSalary + $premi + $mealAllowance - $lateDeduction - $otherDeduction;

                SalarySlip::create([
                    'salary_period_id' => $salaryPeriod->id,
                    'employee_id' => $employee->id,
                    'basic_salary' => $basicSalary,
                    'premi' => $premi,
                    'meal_allowance' => $mealAllowance,
                    'late_deduction' => $lateDeduction,
                    'other_deduction' => $otherDeduction,
                    'total_salary' => $totalSalary,
                    'status' => 'draft',
                    'details' => [
                        'work_days' => $paidDays,
                        'present' => $presentCount,
                        'late' => $lateCount,
                    ]
                ]);
            }
            DB::commit();
            return redirect()->back()->with('success', 'Slip gaji berhasil digenerate.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal generate slip: ' . $e->getMessage());
        }
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
