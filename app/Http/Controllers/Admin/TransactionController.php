<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->status) {
            $transactions = Transaction::with('wallet.user')->whereStatus($request->status)->latest()->paginate(30);
        } else {

            $transactions = Transaction::with('wallet.user')->latest()->paginate(30);
        }
        return response()->json([
            "success" => true,
            "message" => "Transactions retrieved successfully",
            "data" => [
                "transactions" => TransactionResource::collection($transactions)
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->status) {
            $transactions = Transaction::with('wallet.user')->whereStatus($request->status)->latest()->paginate(30);
        } else {

            $transactions = Transaction::with('wallet.user')->latest()->paginate(30);
        }
        return response()->json([
            "success" => true,
            "message" => "Transactions retrieved successfully",
            "data" => [
                "transactions" => TransactionResource::collection($transactions)
            ]
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        //
        $transaction->load('user', 'wallet.bankAccount');
        return response()->json([
            "success" => true,
            "data" => [
                "transaction" => new TransactionResource($transaction)
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
