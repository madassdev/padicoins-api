<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankAccount;
use App\Services\Paystack;
use Exception;
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

        $bank = Bank::find($request->bank_id);

        // Check from Paystack
        try {
            $data = Paystack::getBankDetails($request->account_number, $bank);
        } catch (Exception) {
            return response()->json(['success' => false, 'message' => 'Unknown Error occurred'], 400);
        }

        if (!$data->status) {
            return response()->json(["success" => false, "message" => $data->message]);
        }

        // Check for existing user with the account number

        $existing_account_details = @BankAccount::whereAccountNumber($request->account_number)->with('user', 'bank')->first()->user;

        return response()->json([
            "success" => true,
            "message" => "Account number verified successfully",
            "data" => [
                "account_number" => $data->data->account_number,
                "account_name" => $data->data->account_name,
                "user_details" => $existing_account_details,
            ],
        ]);
    }
}
