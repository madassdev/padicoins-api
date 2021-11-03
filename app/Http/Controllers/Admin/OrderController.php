<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    //
    public function index()
    {
        $orders = Order::with('user', 'bankAccount', 'coin')->latest()->paginate(20);

        return response()->json([
            "success" => true,
            "message" => "Orders retrieved successfully!",
            "data" => [
                "orders" => OrderResource::collection($orders)
            ]
        ]);
    }

    public function show($track_id)
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

    public function payout($track_id, $force = false, Request $request)
    {
        $order = Order::whereTrackId($track_id)->first();
        if (!$order) {
            return response()->json(['success' => false, 'message' => "Order with Track ID: $track_id not found!"], 404);
        }
        if ($order->status !== "received") {
            if(strtolower($force) !== "force"){
                return response()->json([
                    "success" => false,
                    "message" => "Order has not been received. Please use the force payout route to approve anyway.",
                    "payout_route" => route('payout.force', ["track_id" => $track_id, "force" => "force"])
                ]);
            }
        }

        $request->validate([
            "amount_paid" => "required|numeric|min:0", 
            "currency_paid" => "required|sometimes|in:NGN,USD", 
        ]);

        $order->status = "paid";
        $order->paid_at = Carbon::now();
        $order->complete = true;
        $order->amount_paid = $request->amount_paid;
        $order->currency_paid = $request->currency_paid ?? 'NGN';
        
        $order->save();

        return response()->json([
            "success" => true,
            "message" => "Order paid out successfully. Order has been marked as complete",
            "data" => [
                "order" => new OrderResource($order->refresh()->load('user', 'bankAccount', 'coin', 'user'))
            ]
        ]);

        // Notify Admin
        // Notify User
        //

    }
}
