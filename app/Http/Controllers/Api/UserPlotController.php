<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Office;
use App\Models\Roles;
use App\Models\User;
use App\Models\UserOfficeRole;
use Illuminate\Http\Request;

class UserPlotController extends Controller
{
    public function index()
    {
        $users = User::all();
        $offices = Office::all();
        $roles = Roles::all();
        $plots = UserOfficeRole::with(['user', 'office', 'role'])
            ->get()
            ->map(function ($plot) {
                return (object) [
                    'id' => $plot->id,
                    'user_id' => $plot->user_id,
                    'office_id' => $plot->office_id,
                    'role_id' => $plot->role_id,
                    'user_name' => $plot->user ? $plot->user->name : null,
                    'office_name' => $plot->office ? $plot->office->name : null,
                    'role_name' => $plot->role ? $plot->role->name : null,
                    'is_sales' => $plot->user ? $plot->user->is_sales : false,
                    'created_at' => $plot->created_at,
                    'updated_at' => $plot->updated_at,
                ];
            });

        return view('UserPlot.index', compact('users', 'offices', 'roles', 'plots'));
    }

    public function store(Request $request)
    {
        // Update user's is_sales status
        User::where('id', $request->user_id)->update([
            'is_sales' => $request->has('is_sales') ? true : false,
        ]);

        UserOfficeRole::create([
            'user_id' => $request->user_id,
            'office_id' => $request->office_id,
            'role_id' => $request->role_id,
        ]);

        return back()->with('success', 'User berhasil diplot ke kantor');
    }

    public function show($id)
    {
        $plot = UserOfficeRole::with(['user'])
            ->where('id', $id)
            ->first()
            ->map(function ($plot) {
                return (object) [
                    'id' => $plot->id,
                    'user_id' => $plot->user_id,
                    'office_id' => $plot->office_id,
                    'role_id' => $plot->role_id,
                    'is_sales' => $plot->user ? $plot->user->is_sales : false,
                    'created_at' => $plot->created_at,
                    'updated_at' => $plot->updated_at,
                ];
            });

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

        UserOfficeRole::where('id', $id)->update([
            'user_id' => $request->user_id,
            'office_id' => $request->office_id,
            'role_id' => $request->role_id,
        ]);

        return back()->with('success', 'Plotting berhasil diperbarui');
    }

    public function destroy($id)
    {
        UserOfficeRole::where('id', $id)->delete();

        return back()->with('success', 'Plotting berhasil dihapus');
    }
}
