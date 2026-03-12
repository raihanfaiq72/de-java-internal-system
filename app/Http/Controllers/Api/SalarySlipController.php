<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SalarySlip;
use Illuminate\Http\Request;

class SalarySlipController extends Controller
{
    public function show(SalarySlip $salarySlip)
    {
        $salarySlip->load(['employee', 'salaryPeriod']);

        return view('Salary.slips.show', compact('salarySlip'));
    }

    public function print(SalarySlip $salarySlip)
    {
        $salarySlip->load(['employee', 'salaryPeriod']);

        return view('Salary.slips.print', compact('salarySlip'));
    }

    public function update(Request $request, SalarySlip $salarySlip)
    {
        $request->validate([
            'basic_salary' => 'nullable|numeric',
            'meal_allowance' => 'nullable|numeric',
            'late_deduction' => 'nullable|numeric',
            'work_days' => 'nullable|integer|min:0',
            'daily_basic_salary' => 'nullable|numeric|min:0',
            'premi' => 'nullable|numeric|min:0',
            'insentif' => 'nullable|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'job_allowance' => 'nullable|numeric|min:0',
            'attendance_premi' => 'nullable|numeric|min:0',
            'overtime' => 'nullable|numeric|min:0',
            'loan_deduction' => 'nullable|numeric|min:0',
            'kasbon_deduction' => 'nullable|numeric|min:0',
            'bpjs' => 'nullable|numeric|min:0',
            'other_deduction' => 'nullable|numeric|min:0',
        ]);

        $basic = $request->basic_salary;
        if ($basic === null) {
            $days = (int) ($request->work_days ?? 0);
            $daily = (float) ($request->daily_basic_salary ?? 0);
            $basic = $days * $daily;
        }
        $premi = (float) ($request->premi ?? (($request->insentif ?? 0) + ($request->attendance_premi ?? 0) + ($request->transport_allowance ?? 0) + ($request->job_allowance ?? 0) + ($request->overtime ?? 0)));
        $meal = (float) ($request->meal_allowance ?? 0);
        $late = (float) ($request->late_deduction ?? 0);
        $other = (float) (($request->loan_deduction ?? 0) + ($request->kasbon_deduction ?? 0) + ($request->bpjs ?? 0) + ($request->other_deduction ?? 0));
        $total = $basic + $premi + $meal - $late - $other;

        $salarySlip->update([
            'basic_salary' => $basic,
            'premi' => $premi,
            'meal_allowance' => $meal,
            'late_deduction' => $late,
            'other_deduction' => $other,
            'total_salary' => $total,
            'details' => [
                'work_days' => (int) ($request->work_days ?? ($salarySlip->details['work_days'] ?? 0)),
                'daily_rate' => (float) ($request->daily_basic_salary ?? ($salarySlip->details['daily_rate'] ?? 0)),
            ],
        ]);

        return redirect()->back()->with('success', 'Slip gaji diperbarui.');
    }

    public function publish(SalarySlip $salarySlip)
    {
        $salarySlip->update(['status' => 'published']);

        return redirect()->back()->with('success', 'Slip gaji dipublish.');
    }

    public function destroy(SalarySlip $salarySlip)
    {
        if ($salarySlip->status === 'paid') {
            return redirect()->back()->with('error', 'Slip gaji yang sudah dibayar tidak dapat dihapus.');
        }

        $salarySlip->delete();

        return redirect()->back()->with('success', 'Slip gaji dihapus.');
    }
}
