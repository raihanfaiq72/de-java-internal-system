<?php

namespace App\Http\Controllers;

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
        // Allow manual adjustments
        $request->validate([
            'basic_salary' => 'required|numeric',
            'premi' => 'required|numeric',
            'meal_allowance' => 'required|numeric',
            'late_deduction' => 'required|numeric',
            'other_deduction' => 'required|numeric',
        ]);

        $total = $request->basic_salary + $request->premi + $request->meal_allowance - $request->late_deduction - $request->other_deduction;

        $salarySlip->update([
            'basic_salary' => $request->basic_salary,
            'premi' => $request->premi,
            'meal_allowance' => $request->meal_allowance,
            'late_deduction' => $request->late_deduction,
            'other_deduction' => $request->other_deduction,
            'total_salary' => $total,
        ]);

        return redirect()->back()->with('success', 'Slip gaji diperbarui.');
    }

    public function publish(SalarySlip $salarySlip)
    {
        $salarySlip->update(['status' => 'published']);
        return redirect()->back()->with('success', 'Slip gaji dipublish.');
    }
}
