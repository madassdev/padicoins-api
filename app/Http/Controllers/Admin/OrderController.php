<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Resources\WalletResource;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\Wallet;
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

    public function payout($track_id, $force = false, Request $request)
    {
        $wallet = Wallet::whereTrackId($track_id)->first();
        if (!$wallet) {
            return response()->json(['success' => false, 'message' => "Order with Track ID: $track_id not found!"], 404);
        }
        // if ($wallet->status !== "received") {
        //     if(strtolower($force) !== "force"){
        //         return response()->json([
        //             "success" => false,
        //             "message" => "Order has not been received. Please use the force payout route to approve anyway.",
        //             "payout_route" => route('payout.force', ["track_id" => $track_id, "force" => "force"])
        //         ]);
        //     }
        // }

        $request->validate([
            "amount_paid" => "required|numeric|min:0",
            "currency_paid" => "required|sometimes|in:NGN,USD",
            "hash" => "required"
        ]);

        $t = $wallet->saveState($wallet->fetchState());
        $transaction = Transaction::whereHash($request->hash)->first();
        if ($transaction) {

            $transaction->amount_paid = $request->amount_paid;
            $transaction->paid_at = Carbon::now();
            $transaction->payment_status  = 'paid';
            $transaction->complete = true;
            $transaction->save();
        }else{
            return response()->json([
                "success" => false,
                "message" => "Transaction with Hash: {$request->hash} not found in Wallet address: {$wallet->address}"
            ], 404);
        }
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
