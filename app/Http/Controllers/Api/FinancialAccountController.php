<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FinancialAccount;
use Illuminate\Http\Request;

class FinancialAccountController extends Controller
{
    public function index(Request $request)
    {
        $query = FinancialAccount::where('office_id', session('active_office_id'));

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $data = $query->orderBy('code')->get();

        return apiResponse(true, 'Data Financial Accounts', $data);
    }

    public function show($id)
    {
        $data = FinancialAccount::where('office_id', session('active_office_id'))->find($id);
        
        if (!$data) {
            return apiResponse(false, 'Data not found', null, null, 404);
        }

        return apiResponse(true, 'Detail Financial Account', $data);
    }
}
