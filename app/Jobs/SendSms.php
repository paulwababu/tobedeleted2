<?php

namespace App\Jobs;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use GuzzleHttp\Client;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSms implements ShouldQueue
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
    public function handle(): void
    {
        try {
            $client = new Client();
            $response = $client->request('POST', 'https://api.mobilesasa.com/v1/send/message', [
                'form_params' => [
                    'senderID' => $this->data['sms_sender_id'],
                    'phone' => $this->data['msisdn'],
                    'message' => $this->data['message']
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => 'Bearer ' . $this->data['sms_bearer_token']
                ],
            ]);

            $mobilesasa_api_response = $response->getBody()->getContents();
            $formatted_response = json_decode($mobilesasa_api_response);
//            Log::info($mobilesasa_api_response);
//            Log::info($formatted_response->responseCode);
            if ($formatted_response->responseCode == '0200'){
                Message::create([
                    'company_id' => $this->data['company_id'],
                    'msisdn' => $this->data['msisdn'],
                    'message' => $this->data['message'],
                    'api_response_message' => $formatted_response->message,
                    'tracking_id' => $formatted_response->messageId,
                    'status' => 1
                ]);
            } else {
                Message::create([
                    'company_id' => $this->data['company_id'],
                    'msisdn' => $this->data['msisdn'],
                    'message' => $this->data['message'],
                    'api_response_message' => $formatted_response->message,
                    'status' => 0
                ]);
            }
        } catch (\Exception $e){
            Log::info($e->getMessage());
        }
    }
}
