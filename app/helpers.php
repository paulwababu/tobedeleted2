<?php

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

// validate phone
if (!function_exists('validateMsisdn')){
    function validateMsisdn($phone): array
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $kenyaNumberProto = $phoneUtil->parse($phone, "KE");
            $isValid = $phoneUtil->isValidNumber($kenyaNumberProto);
            if ($isValid) {
                $phone = $phoneUtil->format($kenyaNumberProto, PhoneNumberFormat::E164);
                return [
                    'isValid' => $isValid,
                    'msisdn' => substr($phone, 1)
                ];
            }
            return [
                'isValid' => $isValid,
                'msisdn' => $phone
            ];
        } catch (NumberParseException $e) {
            return [
                'isValid' => false,
                'msisdn' => $phone
            ];
        }
    }
}

// generate random mumbers
if (!function_exists('generateNumbers')){
    function generateNumbers($digits = 6): string
    {
        $i = 0;
        $x = "";
        while($i < $digits){
            $x .= mt_rand(0, 9);
            $i++;
        }
        return $x;
    }
}

// stk push
if (!function_exists('stkPush')){
    function stkPush($stkAmount, $stkPhone, $account_number){
        try {
            $client = new Client();
            $response = $client->request('POST', url('api/v1/stk/push'), [
                'form_params' => [
                    'amount' => $stkAmount,
                    'phone' => $stkPhone,
                    'account_number' => $account_number,
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
            ]);
            Log::channel('stklog')->info($response->getBody());
        } catch (\Exception $e){
            Log::channel('stklog')->info($e->getMessage());
        }
    }
}

// generate m-pesa security credential
if (!function_exists('getMpesaSecurityCredential')) {
    function getMpesaSecurityCredential($password, $devMode = true)
    {
        if (Cache::has('encrypted_mpesa_security_credential')) {
            return decrypt(Cache::get('encrypted_mpesa_security_credential'));
        }

        $key = file_get_contents(storage_path('mpesa_certificates/prod_cert.cer.txt'));

        if ($devMode){
            $key = file_get_contents(storage_path('mpesa_certificates/sandbox_cert.cer.txt'));
        }

        openssl_public_encrypt($password, $encrypted, $key, OPENSSL_PKCS1_PADDING);

        $encryptedCredentials = base64_encode($encrypted);

        Cache::forever('encrypted_mpesa_security_credential', encrypt($encryptedCredentials));

        return $encryptedCredentials;
    }
}

if (!function_exists('checkBearerTokenAndSenderIdStatus')){
    function checkBearerTokenAndSenderIdStatus($sms_channel, $sms_bearer_token, $sms_sender_id): array
    {
        // custom mobilesasa settings
        if ($sms_channel == 2){
            if (empty($sms_bearer_token)){
                return [
                    'status' => false,
                    'message' => 'Update company "SMS BEARER TOKEN" to send SMS'
                ];
            } elseif (empty($sms_sender_id)){
                return [
                    'status' => false,
                    'message' => 'Update company "SMS SENDER ID" to send SMS'
                ];
            } else {
                return [
                    'status' => true,
                    'message' => '"SMS BEARER TOKEN" and "SMS SENDER ID" are set'
                ];
            }
        // system mobilesasa settings
        } else {
            return [
                'status' => true,
                'message' => '"SMS BEARER TOKEN" and "SMS SENDER ID" are set'
            ];
        }
    }
}
