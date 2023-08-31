<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BillController extends Controller
{
    // create bill
    public function create(Request $request, Tenant $tenant)
    {
        $request->validate([
            'type' => ['required', 'numeric', Rule::in([1,2])],
            'bill_month' => ['required', 'date'],
            'units_consumed' => ['required', 'numeric', 'min:0'],
            'cost_per_unit' => ['required', 'numeric', 'min:0'],
            'amount_paid' => ['required', 'numeric', 'max:'.$request->input('units_consumed') * $request->input('cost_per_unit')]
        ]);

        $bill_desc = $request->input('type') == 1 ? 'Water' : 'Electric';

        $bill = Bill::where('tenant_id', $tenant->id)->where('type', $request->input('type'))
            ->where('bill_month', Carbon::parse($request->input('bill_month'))->format('F').' '.now()->year)->first();
        if ($bill){
            return response()->json([
                'status' => false,
                'message' => $bill_desc. ' Bill already exists for tenant '.$tenant->name. ' for the month '.Carbon::parse($request->input('bill_month'))->format('F'). ' '.now()->year
            ]);
        }

        $amount_payable = $request->input('units_consumed') * $request->input('cost_per_unit');

        $tenant->bills()->create([
             'type' => $request->input('type'),
             'bill_month' => Carbon::parse($request->input('bill_month'))->format('F'). ' '.now()->year,
             'units_consumed' => $request->input('units_consumed'),
             'cost_per_unit' => $request->input('cost_per_unit'),
             'amount_payable' => $amount_payable,
             'amount_paid' => $request->input('amount_paid'),
             'balance' => $amount_payable - $request->input('amount_paid'),
             'payment_status' => $request->input('amount_paid') == $amount_payable ? true : false,
         ]);

        // TODO alert tenant

        return response()->json([
            'status' => true,
            'message' => $bill_desc.' Bill for tenant '.$tenant->name. ' for the month '.Carbon::parse($request->input('bill_month'))->format('F'). ' '.now()->year. ' created successfully'
        ]);
    }

    public function historyPage(Tenant $tenant)
    {
        return $tenant->bills()->paginate(50);
    }
}
