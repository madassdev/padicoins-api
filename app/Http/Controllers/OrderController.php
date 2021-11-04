<?php

namespace App\Http\Controllers;

use App\Http\Resources\BankResource;
use App\Http\Resources\CoinResource;
use App\Http\Resources\OrderResource;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Coin;
use App\Models\Order;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WebhookCallback;
use App\Notifications\CryptoReceivedNotification;
use App\Services\Crypto;
use App\Services\Paystack;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Throwable;

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
            "coin_id" => "required|exists:coins,id",
        ]);

        // Save User
        $user = User::firstOrCreate(['email' => $request->email], [
            "name" => $request->name,
            "email" => $request->email,
            "mobile" => $request->mobile,
            "password" => bcrypt($request->mobile),
        ]);

        $bank = Bank::find($request->bank_id);
        $coin = Coin::find($request->coin_id);

        // Validate from Paystack
        // try {
        //     $data = Paystack::getBankDetails($request->account_number, $bank);
        // } catch (Exception) {
        //     return response()->json(['success' => false, 'message' => 'Unknown Error occurred'], 400);
        // }

        // if (!@$data->status) {
        //     return response()->json(["success" => false, "message" => @$data->message ?? "Unknown error occured"], 400);
        // }

        // Save Bank
        // $bank_account = $user->bankAccounts()->updateOrcreate([
        //     "account_number" => $request->account_number
        // ], [
        //     "bank_id" => $request->bank_id,
        //     "account_number" => $request->account_number,
        //     "bank_name" => $bank->name,
        //     "account_name" => $data->data->account_name
        // ]);
        $bank_account = $user->bankAccounts->first();
        $wallet = $coin->createWallet($user);
        // Place Order
        $order = $user->orders()->create([
            "bank_account_id" => $bank_account->id,
            "track_id" => $wallet->track_id,
            "wallet_address" => $wallet->address,
            "coin_id" => $coin->id,
            "coin_symbol" => $coin->title,
            "api_data" => $wallet,
        ]);

        return response()->json([
            "success" => true,
            "message" => "Order initialized successfully",
            "data" => [
                "order" => new OrderResource($order->refresh()->load('user', 'coin', 'bankAccount')),
                // "order" => $order
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

    public function banks()
    {
        $banks = Bank::all();
        return response()->json([
            "success" => true,
            "data" => [
                "banks" => BankResource::collection($banks)
            ]
        ]);
    }

    public function coins()
    {
        $banks = Coin::all();
        return response()->json([
            "success" => true,
            "data" => [
                "coins" => CoinResource::collection($banks)
            ]
        ]);
    }

    public function orderCallBack($track_id, Request $request)
    {
        $wcb = WebhookCallback::create([
            "payload" => ['url'=>$request->fullUrl(), 'body' => $request->all()],
        ]);
        $order = Order::whereTrackId($track_id)->first();

        if (!$order) {
            // Notify Admin of received webhook that does not match an existing order.
            // Save callback data
        }

        if ($order && $order->status !== 'pending') {
            // Payment has been received, meanwhile order is not pending at the moment
            //Notify Admin

        }


        // Validate transaction
        $crypto = new Crypto($order->coin);
        $transaction = $crypto->makeTransaction($order->wallet_address);

        // Save to database
        if ($transaction->success) {

            $order->status = 'received';
            $order->received_at = Carbon::now();
            $order->amount_received = $transaction->amount_in_btc;
            $order->amount_in_usd = $transaction->amount_in_usd;
            $order->amount_in_ngn = $transaction->amount_in_ngn;
            $order->callback_data = ['url' => $request->fullUrl(), 'body' => $request->all()];
            $order->transaction_data = $transaction;
            $order->save();

            // Notify Admin
            $admins = User::role('admin')->get();
            // return $admins;
            Notification::send($admins, new CryptoReceivedNotification($order));
            // try{

            // }catch(Throwable $th){
            //     // Save to db
            // }
            return response()->json([
                "success" => true,
                "message" => "Payment transaction validated successfully!",
                "data" => [
                    "order" => $order
                ]
            ]);
        } else {
            // A Failed transaction
            // Notiy Admin
            // Handle Accordingly
        }
    }

    public function trackOrder($track_id)
    {
        $order = Order::whereTrackId($track_id)->first();
        if (!$order) {
            return response()->json(['success' => false, 'message' => "Order with Track ID: $track_id not found!"], 404);
        }

        return response()->json([
            "success" => true,
            "message" => "Order with Track ID: $track_id retrieved successfully.",
            "data" => [
                "order" => new OrderResource($order->refresh()->load('coin', 'bankAccount')),
            ]
        ]);
    }
}
