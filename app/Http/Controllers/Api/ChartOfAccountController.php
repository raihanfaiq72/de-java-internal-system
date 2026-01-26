<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartOfAccountController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('chart_of_accounts');

        if ($request->has('is_kas_bank')) {
            $query->where('is_kas_bank', $request->is_kas_bank);
        }

        $data = $query->get();

        return apiResponse(true, 'Data Chart of Accounts', $data);
    }
}
