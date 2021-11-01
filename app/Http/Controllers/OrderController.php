<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    //

    public function order(Request $request)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "bank_name" => "required|string|max:255",
            "account_number" => "required|numeric|digits:10",
            "email" => "required|email|max:255",
            "mobile" => "required|numeric|digits:11"
        ]);

        // Save User



        return $request;
    }

    public function verifyBank(Request $request)
    {
        $request->validate([
            "bank_id" => "required|exists:banks,id",
            "account_number" => "required|numeric|digits:10"
        ]);

        // Check from Paystack


        // Get User with existing account

        return $request;
    }
}
