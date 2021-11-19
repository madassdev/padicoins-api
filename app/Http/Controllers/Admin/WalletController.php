<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\WalletResource;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Notifications\TransactionSuccessNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    //
    public function index()
    {
        $wallets = Wallet::with('user', 'bankAccount', 'coin')->latest()->paginate(20);

        return response()->json([
            "success" => true,
            "message" => "Wallets retrieved successfully!",
            "data" => [
                "wallets" => WalletResource::collection($wallets)
            ]
        ]);
    }

    public function show($track_id)
    {
        $wallet = Wallet::whereTrackId($track_id)->with('user', 'transactions')->first();
        if (!$wallet) {
            return response()->json(['success' => false, 'message' => "Wallet with Track ID: $track_id not found!"], 404);
        }

        return response()->json([
            "success" => true,
            "message" => "Wallet with Track ID: $track_id retrieved successfully.",
            "data" => [
                "order" => new WalletResource($wallet->refresh()->load('coin', 'bankAccount')),
            ]
        ]);
    }

    public function payout($track_id, Request $request)
    {
        $request->validate([
            "transaction_id" => "required|exists:transactions,id",
            "amount_paid" => "required|numeric|min:0",
            "currency_paid" => "required|sometimes|in:NGN,USD",
        ]);

        $wallet = Wallet::whereTrackId($track_id)->with('user', 'transactions')->first();
        
        $transaction = $wallet->transactions->find($request->transaction_id);

        if(!$transaction)
        {
            return response()->json([
                "success" => false,
                "message" => "Transaction Id: {$request->transaction_id} does not exist on the wallet with Track Id: {$track_id}"
            ],404);
        }
        if (!$transaction->complete) {
            return response()->json([
                "success" => false,
                "message" => "This Transaction has already been paid out and marked complete."
            ], 403);
        }
        $transaction->update([
            "amount_paid" => $request->amount_paid,
            "currency_paid" => $request->currency_paid ?? "NGN",
            "status" => "complete",
            "completed_at" => Carbon::now(),
            "complete" => true
        ]);

        $transaction->user->notify(new TransactionSuccessNotification($transaction));

        return response()->json([
            "success" => true,
            "message" => "Wallet transaction paid out successfully. Transaction has been marked as complete",
            "data" => [
                "transaction" => $transaction
            ]
        ]);

        // Notify Admin
        // Notify User
        //

    }
}
