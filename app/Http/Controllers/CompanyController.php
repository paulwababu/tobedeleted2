<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Rules\ValidateMsisdn;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    // update page
    public function updatePage(Request $request)
    {
        $company = Company::where('id', $request->user()->company_id)->first();
        $company_uuid = $company->uuid;
        return view('update-company', compact('company_uuid'));
    }

    // load company metadata
    public function loadCompanyMetadata(Company $company)
    {
        return response()->json([
            'uuid' => $company->uuid,
            'name' => $company->name,
            'msisdn' => $company->msisdn,
            'email' => $company->email,
            'location' => $company->location,
            'wallet_balance' => $company->wallet_balance,
            'wallet_account_no' => $company->wallet_account_no
        ]);
    }

    // update
    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'msisdn' => ['required', 'string', new ValidateMsisdn(false)],
            'email' => ['required', 'email'],
            'location' => ['required', 'string']
        ]);

        $company->update([
            'name' => $request->input('name'),
            'msisdn' => $request->input('msisdn'),
            'email' => $request->input('email'),
            'location' => $request->input('location')
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Company updated successfully'
        ]);
    }
}
