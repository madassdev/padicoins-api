<?php

namespace App\Http\Controllers;

use App\Exceptions\ReportableException;
use App\Http\Resources\BankResource;
use App\Http\Resources\CoinResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\WalletResource;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Coin;
use App\Models\Order;
// use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\WebhookCallback;
use App\Notifications\CryptoReceivedNotification;
use App\Notifications\WalletReadyNotification;
use App\Notifications\WebhookCallbackReceivedNotification;
use App\Services\Crypto;
use App\Services\Paystack;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Throwable;

class WalletController extends Controller
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

        $bank_account = BankAccount::whereAccountNumber($request->account_number)->whereBankId($bank->id)->first();

        if (!$bank_account) {

            // Validate from Paystack
            try {
                $data = Paystack::getBankDetails($request->account_number, $bank);
            } catch (Exception) {
                return response()->json(['success' => false, 'message' => 'Unknown Error occurred'], 400);
            }

            if (!@$data->status) {
                return response()->json(["success" => false, "message" => @$data->message ?? "Unknown error occured"], 400);
            }

            // Save Bank
            $bank_account = $user->bankAccounts()->firstOrCreate([
                "bank_id" => $request->bank_id,
                "account_number" => $request->account_number
            ], [
                "bank_id" => $request->bank_id,
                "account_number" => $request->account_number,
                "bank_name" => $bank->name,
                "account_name" => @$data->data->account_name
            ]);
        }



        // Create Wallet
        $wallet = Wallet::whereUserId($user->id)->whereBankAccountId($bank_account->id)->whereCoinId($coin->id)->first();
        if (!$wallet) {
            $wallet = $coin->createWallet($user, $bank_account);
        }
        if ($request->mock_address) {
            $wallet = $coin->createMockWallet($user, $bank_account, $request->mock_address);
        }

        //Notify user
        $user->notify(new WalletReadyNotification($wallet));

        return response()->json([
            "success" => true,
            "message" => "Wallet Initialized successfully",
            "data" => [
                "wallet" => new WalletResource($wallet->refresh()->load('user', 'coin', 'bankAccount')),
            ]
        ]);
    }

    public function orderCallBack($track_id, Request $request)
    {
        $hash = $request->hash ?? "no-hash-in-request-".Carbon::now();
        $wcb = WebhookCallback::create([
            "hash" => $hash,
            "payload" => ['url' => $request->fullUrl(), 'body' => $request->all(), 'header' => $request->header(), 'ip' => $request->ip(),],
        ]);

        $wallet = Wallet::with('user', 'coin', 'bankAccount')->whereTrackId($track_id)->first();

        if ($request->mock_address) {
            $wallet = Wallet::whereAddress($request->mock_address)->first();
        }
        if (!$wallet) {
            // Notify Admin of received webhook that does not match an existing order.
            // Save callback data
            $wallet = Wallet::latest()->first();
            return response()->json([], 400);
        }
        
        $wcb->wallet_id = $wallet->id;
        $wcb->save();
        $block_transaction = collect($wallet->fetchState()->transactions)->where("tx_hash", $hash)->first();

        if($block_transaction && $block_transaction['tx_input_n'] < 0 && $request->hash){
            $amount_received = $wallet->getBaseValue($block_transaction['value']);
            $transaction = $wallet->transactions()->updateOrCreate(['hash' => $hash], [
                "user_id" => $wallet->user_id,
                "hash" => $request->hash,
                "reference" => 'tx-' . $wallet->track_id . '-' . Str::random(3),
                "amount_received" => $amount_received,
                "currency_received" => $request->currency_received ?? $wallet->coin->symbol,
            ]);
        }

        // Save to database
        $admins = User::role('admin')->get();
        Notification::send($admins, new WebhookCallbackReceivedNotification($transaction, $wcb));
        try {
        } catch (Throwable $th) {
            // Save to db
            $err = $th;
        }
        return response()->json([
            "success" => true,
            "message" => "Callback processed successfully",
        ]);
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



    public function trackOrder($track_id)
    {
        $wallet = Wallet::whereTrackId($track_id)->first();
        if (!$wallet) {
            return response()->json(['success' => false, 'message' => "Wallet with Track ID: $track_id not found!"], 404);
        }
        try {
            // $t = $wallet->saveState($wallet->fetchState());
        } catch (Throwable $th) {
            throw new ReportableException($th);
        }

        return response()->json([
            "success" => true,
            "message" => "Wallet with Track ID: $track_id retrieved successfully.",
            "data" => [
                "wallet" => new WalletResource($wallet->refresh()->load('coin', 'bankAccount', 'walletTransactions', 'transactions')),
            ]
        ]);
    }
}
