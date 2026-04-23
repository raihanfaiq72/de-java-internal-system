<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Office;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserPlotController extends Controller
{
    public function index()
    {
        $users = User::all();
        $offices = Office::all();
        $roles = Roles::all();
<<<<<<< Updated upstream
        $plots = DB::table('user_office_roles')
            ->join('users', 'user_office_roles.user_id', '=', 'users.id')
            ->join('offices', 'user_office_roles.office_id', '=', 'offices.id')
            ->join('roles', 'user_office_roles.role_id', '=', 'roles.id')
            ->select('user_office_roles.*', 'users.name as user_name', 'offices.name as office_name', 'roles.name as role_name', 'users.is_sales')
            ->get();
=======
        $plots = UserOfficeRole::with(['user', 'office', 'role'])
            ->latest()
            ->paginate(10);
            
        // Map the paginator's items
        $plots->getCollection()->transform(function ($plot) {
            return (object) [
                'id' => $plot->id,
                'user_id' => $plot->user_id,
                'office_id' => $plot->office_id,
                'role_id' => $plot->role_id,
                'user_name' => $plot->user ? $plot->user->name : null,
                'user_email' => $plot->user ? $plot->user->email : null,
                'office_name' => $plot->office ? $plot->office->name : null,
                'role_name' => $plot->role ? $plot->role->name : null,
                'is_sales' => $plot->user ? $plot->user->is_sales : false,
                'created_at' => $plot->created_at,
                'updated_at' => $plot->updated_at,
            ];
        });
>>>>>>> Stashed changes

        return view('UserPlot.index', compact('users', 'offices', 'roles', 'plots'));
    }

    public function store(Request $request)
    {
        // Update user's is_sales status
        User::where('id', $request->user_id)->update([
            'is_sales' => $request->has('is_sales') ? true : false,
        ]);

        DB::table('user_office_roles')->insert([
            'user_id' => $request->user_id,
            'office_id' => $request->office_id,
            'role_id' => $request->role_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'User berhasil diplot ke kantor');
    }

    public function show($id)
    {
        $plot = DB::table('user_office_roles')
            ->join('users', 'user_office_roles.user_id', '=', 'users.id')
            ->where('user_office_roles.id', $id)
            ->select('user_office_roles.*', 'users.is_sales')
            ->first();

        if (! $plot) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $plot,
        ]);
    }

    public function update(Request $request, $id)
    {
        // Update user's is_sales status
        User::where('id', $request->user_id)->update([
            'is_sales' => $request->has('is_sales') ? true : false,
        ]);

        DB::table('user_office_roles')
            ->where('id', $id)
            ->update([
                'user_id' => $request->user_id,
                'office_id' => $request->office_id,
                'role_id' => $request->role_id,
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Plotting berhasil diperbarui');
    }

    public function destroy($id)
    {
        DB::table('user_office_roles')->where('id', $id)->delete();

        return back()->with('success', 'Plotting berhasil dihapus');
    }
}
