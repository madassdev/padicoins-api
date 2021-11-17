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

class OrderController extends Controller
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

    public function payout($wallet, Transaction $transaction, Request $request)
    {
        $request->validate([
            "amount_paid" => "required|numeric|min:0",
            "currency_paid" => "required|sometimes|in:NGN,USD",
        ]);

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
