<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $employees = Employee::with('user')
            ->when($request->search, function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->search}%")
                    ->orWhere('nik', 'like', "%{$request->search}%");
            })
            ->latest()
            ->paginate(10);

        $users = User::orderBy('name')->get();

        return view('Employees.index', compact('employees', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'required|string|unique:employees,nik',
            'position' => 'required|string',
            'daily_salary' => 'required|numeric|min:0',
            'premi' => 'nullable|numeric|min:0',
            'join_date' => 'nullable|date',
            'user_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive',
        ]);

        Employee::create($request->all());

        return redirect()->back()->with('success', 'Karyawan berhasil ditambahkan');
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'required|string|unique:employees,nik,'.$employee->id,
            'position' => 'required|string',
            'daily_salary' => 'required|numeric|min:0',
            'premi' => 'nullable|numeric|min:0',
            'join_date' => 'nullable|date',
            'user_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive',
        ]);

        $employee->update($request->all());

        return redirect()->back()->with('success', 'Data karyawan berhasil diperbarui');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->back()->with('success', 'Karyawan berhasil dihapus');
    }
}
