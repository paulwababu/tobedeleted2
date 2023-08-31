<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\PendingMpesaTransaction;
use App\Models\RentalPayment;
use App\Models\Transaction;
use App\Models\Unit;
use App\Models\UnknownTransaction;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Response;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class SaveMpesaTransaction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data;
    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): Response
    {
        $ip = $this->data['ip'];

        //TODO whitelist with m-pesa ip addresses
        $paymentResultCode = $this->data['content']['Result']['ResultCode'];

        //check if the transaction was successful
        if($paymentResultCode == 0){
            //check if trans id exists in pending m-pesa transactions
            $result = PendingMpesaTransaction::where('trans_id', $this->data['content']['Result']['ResultParameters']['ResultParameter'][12]['Value'])
                ->where('status', false)->first();

            if ($result){
                $senderDetails = $this->data['content']['Result']['ResultParameters']['ResultParameter'][0]['Value'];

                $name = explode('-', $senderDetails)[1];
                $msisdn = explode('-', $senderDetails)[0];

                /**
                 *  Format the phone depending on response from m-pesa. Use 254 format
                 */
                $phoneUtil = PhoneNumberUtil::getInstance();
                $kenyaNumberProto = $phoneUtil->parse($msisdn, "KE");
                $isValid = $phoneUtil->isValidNumber($kenyaNumberProto);
                if ($isValid) {
                    $new_msisdn = $phoneUtil->format($kenyaNumberProto, PhoneNumberFormat::E164);
                    $formatted_msisdn = substr($new_msisdn, 1);
                } else {
                    $formatted_msisdn = $msisdn;
                }

                $account_no = $result->account_no;
                $trans_id = $this->data['content']['Result']['ResultParameters']['ResultParameter'][12]['Value'];
                $amount = $this->data['content']['Result']['ResultParameters']['ResultParameter'][10]['Value'];
                $trans_time = Carbon::parse($this->data['content']['Result']['ResultParameters']['ResultParameter'][9]['Value'])->toDateTimeString();
                $business_short_code = $result->business_short_code;
                $third_party_trans_id = $result->third_party_trans_id;

                // look up unit
                $unit = Unit::where('account_no', $account_no)->where('status', true)->first();
                if ($unit){
                    $this->processRentPayment($name, $msisdn, $amount, $unit, $business_short_code, $third_party_trans_id, $trans_id, $trans_time, $account_no, $ip, $unit->property->company->uuid);
                } else {
                    UnknownTransaction::create([
                        'channel' => 1,
                        'trans_id' => $trans_id,
                        'trans_time' => $trans_time,
                        'amount' => $amount,
                        'account_no' => $account_no,
                        'msisdn' => $msisdn,
                        'name' => $name
                    ]);
                }

                //update that the transaction has been verified to be from m-pesa
                $result->update([
                    'status' => true
                ]);
            } else {
                Log::error("Transaction ID #$result->trans_id not found");
            }
        } else {
            Log::error('Transaction validation failed');
        }

        //Responding to the confirmation request
        $response = new Response();
        $response->headers->set("Content-Type","text/xml; charset=utf-8");
        $response->setContent(json_encode(["C2BPaymentConfirmationResult"=>"Success"]));
        return $response;
    }

    public function processRentPayment($name, $msisdn, $amount, Unit $unit, $business_short_code, $third_party_trans_id, $trans_id, $trans_time, $account_no, $ip, Company $company): void
    {
        $rental_payment = RentalPayment::where('unit_id', $unit->id)->latest()->first();
        if ($rental_payment){
            if (($amount + $rental_payment->amount_paid) < $rental_payment->amount_payable){
                // amount is less
                $rental_payment->update([
                    'amount_paid' => $rental_payment->amount_paid + $amount
                ]);

                $balance = $rental_payment->amount_payable - $this->loadAmountPaid($rental_payment);
                // update balance
                $rental_payment->update([
                    'balance' => $balance
                ]);

                $message = "$name, your payment of KES $amount was received but it's less than the required amount of KES $rental_payment->amount_payable by KES $balance. Kindly complete your balance. Thank you for choosing us.";
            } elseif (($amount + $this->loadAmountPaid($rental_payment)) == $rental_payment->amount_payable){
                // amount is equal
                $rental_payment->update([
                    'amount_paid' => $rental_payment->amount_paid + $amount,
                    'balance' => 0,
                    'description' => "deposit & rent",
                    'payment_status' => true
                ]);
                $message = "$name, your payment of KES $amount was received. Thank you for choosing us.";
            } else {
                // paid in excess (carry to next month)
                $balance = $rental_payment->amount_payable - ($this->loadAmountPaid($rental_payment) + $amount);
                // close previous balance
                $rental_payment->update([
                    'amount_paid' => $this->loadAmountPaid($rental_payment) + $amount,
                    'balance' => $balance,
                    'payment_status' => true
                ]);

                $surplus = abs($balance);

                RentalPayment::create([
                    'unit_id' => $unit->id,
                    'name' => $name,
                    'msisdn' => $msisdn,
                    'rental_month' => Carbon::parse($rental_payment->rental_month)->addMonth()->format('F').' '.now()->year,
                    'amount_payable' => $unit->rent,
                    'amount_paid' => $surplus,
                    'balance' => $unit->rent - $surplus,
                    'description' => "rent",
                    'payment_status' => false
                ]);
                $message = "$name, your payment of KES $amount was received. The surplus amount of KES $surplus has been credited to next months rent. Thank you for choosing us.";
            }

            Transaction::create([
                'unit_id' => $unit->id,
                'channel' => 1,
                'trans_id' => $trans_id,
                'trans_time' => $trans_time,
                'amount' => $amount,
                'business_short_code' => $business_short_code,
                'account_no' => $account_no,
                'third_party_trans_id' => $third_party_trans_id,
                'msisdn' => $msisdn,
                'name' => $name,
                'ip' => $ip
            ]);

            // send payment confirmation message to tenant
            SendSms::dispatch([
                'msisdn' => $msisdn,
                'message' => $message,
                'company_id' => $company->id,
                'sms_bearer_token' => $company->sms_channel == 1 ? config('mobilesasa.bearer_token') : $company->sms_bearer_token,
                'sms_sender_id' => $company->sms_channel == 1 ? config('mobilesasa.sender_id') : $company->sms_sender_id
            ])->onQueue('rent-easy-send-sms')->onConnection('beanstalkd-worker001');

        } else {
            Log::info("Rental payment channel for unit #$unit->uuid doesnt exist");
        }
    }

    public function loadAmountPaid(RentalPayment $rentalPayment)
    {
        return RentalPayment::where('uuid', $rentalPayment->uuid)->value('amount_paid');
    }
}
