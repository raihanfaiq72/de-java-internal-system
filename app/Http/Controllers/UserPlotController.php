<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Office;
use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserPlotController extends Controller
{
    public function index()
    {
        $users = User::all();
        $offices = Office::all();
        $roles = Roles::all();
        $plots = DB::table('user_office_roles')
            ->join('users', 'user_office_roles.user_id', '=', 'users.id')
            ->join('offices', 'user_office_roles.office_id', '=', 'offices.id')
            ->join('roles', 'user_office_roles.role_id', '=', 'roles.id')
            ->select('user_office_roles.*', 'users.name as user_name', 'offices.name as office_name', 'roles.name as role_name')
            ->get();

        return view('UserPlot.index', compact('users', 'offices', 'roles', 'plots'));
    }

    public function store(Request $request)
    {
        DB::table('user_office_roles')->insert([
            'user_id' => $request->user_id,
            'office_id' => $request->office_id,
            'role_id' => $request->role_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'User berhasil diplot ke kantor');
    }
}