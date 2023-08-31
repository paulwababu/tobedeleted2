<?php

namespace App\Http\Controllers;

use App\Jobs\SendSms;
use App\Models\Message;
use App\Rules\ValidateMsisdn;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    // send single page
    public function sendSinglePage()
    {
        return view('sms.send-single');
    }

    // send single
    public function sendSingle(Request $request)
    {
        $check_response = checkBearerTokenAndSenderIdStatus($request->user()->company->sms_channel, $request->user()->company->sms_bearer_token, $request->user()->company->sms_sender_id);
        if (!$check_response['status']){
            return response()->json([
                'status' => false,
                'message' => $check_response['message']
            ]);
        }

        $request->validate([
            'msisdn' => ['required', 'string', new ValidateMsisdn(false, false)],
            'message' => ['required', 'string', 'max:960']
        ]);

        SendSms::dispatch([
            'msisdn' => $request->input('msisdn'),
            'message' => $request->input('message'),
            'company_id' => $request->user()->company->id,
            'sms_bearer_token' => $request->user()->company->sms_channel == 1 ? config('mobilesasa.bearer_token') : $request->user()->company->sms_bearer_token,
            'sms_sender_id' => $request->user()->company->sms_channel == 1 ? config('mobilesasa.sender_id') : $request->user()->company->sms_sender_id
        ])->onQueue('rent-easy-send-sms')->onConnection('beanstalkd-worker001');

        return response()->json([
            'status' => true,
            'message' => 'Messages sent successfully'
        ]);
    }

    // outgoing sms page
    public function outgoingSmsPage()
    {
        return view('sms.outgoing');
    }

    // load outgoing sms
    public function loadOutgoing(Request $request)
    {
        return Message::where('company_id', $request->user()->company_id)->filter($request)->paginate(50);
    }
}
