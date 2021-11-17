<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Resources\WalletResource;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

    public function payout($track_id, Request $request)
    {
        $wallet = Wallet::whereTrackId($track_id)->first();
        if (!$wallet) {
            return response()->json(['success' => false, 'message' => "Order with Track ID: $track_id not found!"], 404);
        }

        $request->validate([
            "amount_received" => "required|numeric|min:0",
            "amount_paid" => "required|numeric|min:0",
            "currency_paid" => "required|sometimes|in:NGN,USD",
            "hash" => "required"
        ]);

        // $t = $wallet->saveState($wallet->fetchState());

        $transaction = $wallet->transactions()->create([
            "user_id" => $wallet->user_id,
            "hash" => $request->hash,
            "reference" => $wallet->track_id,
            "amount_received" => $request->amount_received,
            "amount_paid" => $request->amount_paid,
            "currency_received" => $request->currency_received ?? $wallet->coin->symbol,
            "currency_paid" => $request->currency_paid ?? "NGN",
            "status" => "complete",
            "completed_at" => Carbon::now(),
        ]);
        
        return response()->json([
            "success" => true,
            "message" => "Wallet transaction paid out successfully. Transaction has been marked as complete",
            "data" => [
                "order" => new WalletResource($wallet->refresh()->load('user', 'bankAccount', 'coin', 'transactions'))
            ]
        ]);

        // Notify Admin
        // Notify User
        //

    }
}
