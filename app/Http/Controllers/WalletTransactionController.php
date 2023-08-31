<?php

namespace App\Http\Controllers;

use App\Jobs\SendMpesaStk;
use App\Models\Company;
use App\Models\WalletTransaction;
use App\Rules\ValidateMsisdn;
use Illuminate\Http\Request;

class WalletTransactionController extends Controller
{
    // manage page
    public function managePage(Request $request)
    {
        $company = Company::where('id', $request->user()->company_id)->first();
        $company_uuid = $company->uuid;
        return view('wallet-transactions', compact('company_uuid'));
    }

    // load for company
    public function loadForCompany(Request $request)
    {
        return WalletTransaction::where('company_id', $request->user()->company_id)->filter($request)->paginate(50);
    }

    // top up wallet
    public function topUpWallet(Request $request, Company $company)
    {
        $request->validate([
            'msisdn' => ['required', 'string', new ValidateMsisdn(false, true)],
            'amount' => ['required', 'numeric', 'min:5', 'max:70000']
        ]);

        SendMpesaStk::dispatch([
            'amount' => $request->input('amount'),
            'msisdn' => $request->input('msisdn'),
            'account_no' => $company->wallet_account_no
        ])->onQueue('send-mpesa-stk')->onConnection('beanstalkd-worker001');

        return response()->json([
            'status' => true,
            'message' => 'M-pesa payment prompt sent to '.$request->input('msisdn').'. Enter your m-pesa pin to complete the transaction'
        ]);
    }
}
