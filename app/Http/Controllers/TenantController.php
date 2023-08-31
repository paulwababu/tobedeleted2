<?php

namespace App\Http\Controllers;

use App\Jobs\SendMpesaStk;
use App\Jobs\SendSms;
use App\Models\Company;
use App\Models\RentalPayment;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\Unit;
use App\Models\User;
use App\Rules\ValidateMsisdn;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class TenantController extends Controller
{
    // manage tenants
    public function managePage()
    {
        return view('manage-tenants');
    }

    // load
    public function load(Request $request)
    {
        $user = $request->user();
        $tenants = Tenant::filter($request)->with(['unit']);
        if ($user->hasRole('owner') || $user->hasRole('agent')) {
            $tenants = $tenants->where('company_id', $request->user()->company_id);
        }
        return $tenants->paginate(20);
    }

    // create
    public function create(Request $request, Unit $unit)
    {
        if($unit->status){
            return response()->json([
                'status' => false,
                'message' => 'Unit '.$unit->label. ' is not vacant at the moment'
            ]);
        }

        $request->validate([
            'no_of_occupants' => ['required', 'numeric'],
            'name' => ['required', 'string'],
            'msisdn' => ['required', 'string', new ValidateMsisdn(false)],
            'email' => ['required', 'email'],
            'national_id_no' => ['required', 'string'],
            'gender' => ['required', 'numeric'],
            'rental_payment_channel' => ['required', 'numeric'],
            'state_on_check_in' => ['required', 'string', 'max:2000'],
            'tenant_agreement_doc_url' => ['required', 'mimes:jpeg,png,jpg,pdf', 'max:1024']
        ]);

        if ($request->hasFile('tenant_agreement_doc_url')){
            if (env('APP_ENV') == 'production'){
                $tenant_agreement_doc_url_extension = $request->file('tenant_agreement_doc_url')->extension();
                $tenant_agreement_doc_url = Storage::disk('do_spaces')->putFileAs('rent_easy/tenants_agreement_docs', $request->file('tenant_agreement_doc_url'), time().'.'.$tenant_agreement_doc_url_extension, 'public');
                $tenant_agreement_doc_url_file_path = 'https://mobilesasa-attachments.fra1.digitaloceanspaces.com/'.$tenant_agreement_doc_url;
            } else {
                $tenant_agreement_doc_url_file_path = "https://via.placeholder.com/640x480.png";
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Property kyc docs & cover photo upload failed. Please select attachments before uploading'
            ]);
        }

        $tenant = Tenant::where('msisdn', $request->input('msisdn'))
                ->where('unit_id', $unit->id)->where('status', 1)->first();
        if ($tenant){
            return response()->json([
                'status' => false,
                'message' => 'Unit '.$unit->label.' already assigned to tenant '.$request->input('msisdn')
            ]);
        }

        // create tenant
        Tenant::create([
            'unit_id' => $unit->id,
            'user_id' => $request->user()->id,
            'company_id' => $request->user()->company_id,
            'name' => $request->input('name'),
            'msisdn' => $request->input('msisdn'),
            'email' => $request->input('email'),
            // check_in = now() (set on migration)
            // status = 1 (set in migration)
            'no_of_occupants' => $request->input('no_of_occupants'),
            'state_on_check_in' => $request->input('state_on_check_in'),
            'tenant_agreement_doc_url' => $tenant_agreement_doc_url_file_path
        ]);

        // update vacancy status
        $unit->update([
            'status' => true
        ]);

        // create rent payment instance
        RentalPayment::create([
            'unit_id' => $unit->id,
            'name' => $request->input('name'),
            'msisdn' => $request->input('msisdn'),
            'rental_month' => now()->format('F').' '.now()->year,
            'amount_payable' => $unit->deposit + $unit->rent,
            'amount_paid' => $request->input('rental_payment_channel') == 1 ? $unit->deposit : $unit->deposit + $unit->rent,
            'balance' => $request->input('rental_payment_channel') == 1 ? $unit->rent : 0,
            'description' => $request->input('rental_payment_channel') == 1 ? "deposit" : "deposit & rent",
            'payment_status' => $request->input('rental_payment_channel') == 1 ? false : true
        ]);

        // create user
        $existing_user = User::where('msisdn', $request->input('msisdn'))->first();
        if (!$existing_user){
            $company = Company::updateOrCreate([
                'msisdn' => $request->input('msisdn'),
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'status' => 1
            ]);
            $password = generateNumbers(8);
            $user = User::create([
                'company_id' => $company->id,
                'name' => $request->input('name'),
                'msisdn' => $request->input('msisdn'),
                'email' => $request->input('email'),
                'status' => 1, // approved
                'national_id_no' => $request->input('national_id_no'),
                'gender' => $request->input('gender'),
                'password' => Hash::make($password)
            ]);
            $user->assignRole('tenant');
            $message = "You have been added as a tenant in {$unit->property->name}. Visit ".route('tenant.load-my-residences'). "to confirm this request using the credentials below;".PHP_EOL."Email = ".$request->input('email').PHP_EOL."Password = ".$password;
        } else {
            $message = "You have been added as a tenant in {$unit->property->name}. Visit ".route('tenant.load-my-residences'). "to confirm this request";
        }

        SendSms::dispatch([
            'msisdn' => $request->input('msisdn'),
            'message' => $message,
            'company_id' => $request->user()->company->id,
            'sms_bearer_token' => $request->user()->company->sms_channel == 1 ? config('mobilesasa.bearer_token') : $request->user()->company->sms_bearer_token,
            'sms_sender_id' => $request->user()->company->sms_channel == 1 ? config('mobilesasa.sender_id') : $request->user()->company->sms_sender_id
        ])->onQueue('rent-easy-send-sms')->onConnection('beanstalkd-worker001');

        return response()->json([
            'status' => true,
            'message' => 'Tenant created successfully'
        ]);
    }

    // my residences page
    public function myResidencesPage()
    {
        return view('my-residences');
    }

    // load my residences
    public function loadMyResidences(Request $request)
    {
        // TODO complete this query
        return Tenant::where('msisdn', $request->user()->msisdn)->with(['unit'])->paginate(50);
    }

    // confirm check in request
    public function confirmCheckInRequest(Tenant $tenant)
    {
        $tenant->update([
            'check_in_confirmation' => true
        ]);

        // TODO send message to tenant

        return response()->json([
            'status' => true,
            'message' => 'Tenant reservation confirmed successfully'
        ]);
    }

    // pay rent via mpesa
    public function payRentViaMpesa(Request $request, Tenant $tenant)
    {
        $request->validate([
            'msisdn' => ['required', 'string', new ValidateMsisdn(false, true)]
        ]);

        SendMpesaStk::dispatch([
            'amount' => $tenant->unit->rent,
            'msisdn' => $tenant->unit->msisdn,
            'account_no' => $tenant->unit->account_no
        ])->onQueue('send-mpesa-stk')->onConnection('beanstalkd-worker001');

        return response()->json([
            'status' => true,
            'message' => 'Mpesa payment prompt sent to '.$tenant->msisdn.'. Enter your mpesa pin to complete the transaction'
        ]);
    }

    // issue vacation notice
    public function issueVacationNotice(Tenant $tenant)
    {
        if (Carbon::parse($tenant->check_in)->diffInDays(now()) < 30){
            return response()->json([
                'status' => false,
                'message' => 'Vacation notice request failed. Your must have stayed for more than 30 days'
            ]);
        }

        $tenant->update([
            'check_out_status' => 1
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Vacation notice requested successfully. We will call you shortly'
        ]);
    }

    // all transactions page
    public function transactionsPage()
    {
        return view('tenant-transactions');
    }

    // load all transactions
    public function loadtransactions(Request $request)
    {
        $unit_ids = Tenant::where('msisdn', $request->user()->msisdn)
            ->where('status', '!=', 2) // hasn't moved out yet
            ->pluck('unit_id');
        return Transaction::whereIn('unit_id', $unit_ids)->with(['unit'])->filter($request)->paginate(50);
    }

    // rental payments page
    public function manageRentalPaymentsPage()
    {
        return view('tenant-rental-payments');
    }

    // load rental payments
    public function loadRentalPayments(Request $request)
    {
        $unit_ids = Tenant::where('msisdn', $request->user()->msisdn)
            ->where('status', '!=', 2) // hasn't moved out yet
            ->pluck('unit_id');
        return RentalPayment::whereIn('unit_id', $unit_ids)->with(['unit'])->filter($request)->paginate(50);
    }
}
