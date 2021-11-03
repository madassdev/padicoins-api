<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
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
}
