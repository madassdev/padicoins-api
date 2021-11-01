<?php

namespace App\Services;

use App\Models\AppConfig;

class Paystack
{

    public static function getSecretKey($test = false)
    {
        $paystack_secret_key = cfg('paystack_secret_key_test');
        return $paystack_secret_key;
    }


    public static function getBankDetails($account_no, $bank)
    {
        $paystack_secret_key = self::getSecretKey();
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/bank/resolve?account_number=$account_no&bank_code={$bank->code}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $paystack_secret_key",
                "Cache-Control: no-cache",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if($err)
        {
            // abort(403, 'Unable to verify from paystack');
            return response()->json([
                "success" => false,
                "message" => 'Unable'
            ]);
        }

        return json_decode($response);
    }
}
