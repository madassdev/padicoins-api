<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Currency;
use App\Models\Order;
use App\Models\User;
use App\Services\Paystack;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    //

    public function order(Request $request)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "bank_id" => "required|exists:banks,id",
            "account_number" => "required|numeric|digits:10",
            "email" => "required|email|max:255",
            "mobile" => "required|numeric|digits:11",
            "currency_id" => "required|exists:currencies,id",
        ]);

        // Save User
        $user = User::updateOrCreate(['email' => $request->email], [
            "name" => $request->name,
            "email" => $request->email,
            "mobile" => $request->mobile,
            "password" => bcrypt($request->mobile),
        ]);

        $bank = Bank::find($request->bank_id);
        $currency = Currency::find($request->currency_id);

        // Validate from Paystack
        try {
            $data = Paystack::getBankDetails($request->account_number, $bank);
        } catch (Exception) {
            return response()->json(['success' => false, 'message' => 'Unknown Error occurred'], 400);
        }

        if (!$data->status) {
            return response()->json(["success" => false, "message" => $data->message], 400);
        }

        // Save Bank
        $bank_account = $user->bankAccounts()->updateOrcreate([
            "account_number" => $request->account_number
        ], [
            "bank_id" => $request->bank_id,
            "account_number" => $request->account_number,
            "bank_name" => $bank->name
        ]);

        //Prepare blockchain
        // {
            $track_id = generate_track_id();
            $callback = route('orders.callback', ['track_id' => $track_id]);
            $wallet_address = "wallet_address_goes_here";
            $data = ["callback" => $callback, "address" => $wallet_address];
        // }
        return $callback;

        // Place Order
        $order = $user->orders()->create([
            "bank_account_id" => $bank_account->id,
            "track_id" => $track_id,
            "wallet_address" => $wallet_address,
            "api_data" => json_encode($data),
        ]);

        return response()->json([
            "success" => true,
            "message" => "Order initialized successfully",
            "data" => [
                "user" => $user,
                "order" => $order->load('currency', 'bankAccount'),
            ]
        ]);

        // Send Mail



        return $request;
    }

    public function verifyBank(Request $request)
    {
        $request->validate([
            "bank_id" => "required|exists:banks,id",
            "account_number" => "required|numeric|digits:10"
        ]);

        $bank = Bank::find($request->bank_id);

        // Validate from Paystack

        try {
            $data = Paystack::getBankDetails($request->account_number, $bank);
        } catch (Exception) {
            return response()->json(['success' => false, 'message' => 'Unknown Error occurred'], 400);
        }

        if (!$data->status) {
            return response()->json(["success" => false, "message" => $data->message], 400);
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

    public function seed()
    {
        

    }

    public function orderCallBack($track_id)
    {
        // Validate transaction
        // Calculate deposit equivalent,
        // Save to database
        // Notify Admin

        return $track_id;
    }
}
