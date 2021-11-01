<?php

function nuban($bank = null)
{
    $banks = [
        '044' => 'Access Bank',
        '023' => 'Citibank',
        '050' => 'Ecobank',
        '011' => 'First Bank of Nigeria',
        '214' => 'FCMB',
        '070' => 'Fidelity Bank',
        '058' => 'Guaranty Trust Bank',
        '030' => 'Heritage Bank',
        '082' => 'Keystone Bank',
        '221' => 'StanbicIBTC',
        '301' => 'Jaiz Bank',
        '068' => 'Standard Chartered Bank',
        '232' => 'Sterling Bank',
        '033' => 'United Bank For Africa',
        '032' => 'Union Bank',
        '035' => 'Wema Bank',
        '057' => 'Zenith Bank',
        '076' => 'Polaris Bank',
        '215' => 'Unity Bank Plc',
        '103' => 'Globus Bank Limited',
        '101' => 'Providus Bank',
        '102' => 'Titan Bank',
        '100' => 'SunTrust Bank',
        '302' => 'TAJ Bank limited',
        '50547' => 'LAPO Microfinance Bank'
    ];
    if ($bank) {

        return @$banks[$bank];
    }

    return $banks;
}

function cfg($key = null){
    $config =  config('app_config');
    if($key){
        return $config[$key];
    }

    return $config;
}