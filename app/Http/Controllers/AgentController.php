<?php

namespace App\Http\Controllers;

use App\Jobs\SendSms;
use App\Models\User;
use App\Rules\ValidateMsisdn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AgentController extends Controller
{
    // create page
    public function manageOrderAgentsPage()
    {
        return view('owner.manage-agents');
    }

    // load owner agents
    public function loadOwnerAgents(Request $request)
    {
        return User::role('agent')->where('company_id', $request->user()->company_id)->filter($request)->paginate(50);
    }

    // create agent
    public function create(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:30'],
            'msisdn' => ['required', 'string', 'max:13', new ValidateMsisdn(true, false, 'User')],
            'email' => ['required', 'email', 'max:30', 'unique:users'],
            'location' => ['required', 'string', 'max:50'],
            'national_id_no' => ['required', 'string', 'max:10', 'unique:users'],
            'gender' => ['required', 'numeric', Rule::in([1, 2, 3])],
        ]);

        $password = generateNumbers(8);
        $user = User::create([
            'company_id' => $request->user()->company_id,
            'name' => $request->input('name'),
            'msisdn' => $request->input('msisdn'),
            'email' => $request->input('email'),
            'location' => $request->input('location'),
            'national_id_no' => $request->input('national_id_no'),
            'gender' => $request->input('gender'),
            'status' => 1, // Approved
            'password' => Hash::make($password)
        ]);

        $user->assignRole('agent');

        // send agent their login credentials
        $message = "You have been added as a agent in. Visit ".route('user.dashboard-page'). "to login using the credentials below;".PHP_EOL."Email = ".$request->input('email').PHP_EOL."Password = ".$request->input('password');
        SendSms::dispatch([
            'msisdn' => $request->input('msisdn'),
            'message' => $message,
            'company_id' => $request->user()->company->id,
            'sms_bearer_token' => $request->user()->company->sms_bearer_token,
            'sms_sender_id' => $request->user()->company->sms_sender_id
        ])->onQueue('rent-easy-send-sms')->onConnection('beanstalkd-worker001');

        return response()->json([
            'status' => true,
            'message' => 'Agent account created successfully. Login credentials have been sent to their phone'
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:30'],
            'msisdn' => ['required', 'string', 'max:13', new ValidateMsisdn(false, false, 'User'), Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', 'max:30', Rule::unique('users')->ignore($user->id)],
            'location' => ['required', 'string', 'max:50'],
            'national_id_no' => ['required', 'string', 'max:10', Rule::unique('users')->ignore($user->id)],
            'gender' => ['required', 'numeric', Rule::in([1, 2, 3])],
        ]);

        $user->update([
            'name' => $request->input('name'),
            'msisdn' => $request->input('msisdn'),
            'email' => $request->input('email'),
            'location' => $request->input('location'),
            'national_id_no' => $request->input('national_id_no'),
            'gender' => $request->input('gender')
        ]);


        return response()->json([
            'status' => true,
            'message' => 'Agent updated successfully'
        ]);
    }

    // approve agent
    public function approve(User $user)
    {
        $user->update([
            'status' => 1
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Agent approved successfully'
        ]);
    }

    // suspend agent
    public function suspend(User $user)
    {
        $user->update([
            'status' => 2
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Agent suspended successfully'
        ]);
    }
}
