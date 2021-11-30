<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AdminController extends Controller
{
    //
    public function analytics()
    {
        // Total Users
        $total_users = User::count();
        $u = [
            "total_users_count" => $total_users
        ];

        // Total Wallets
        $total_wallets = Wallet::count();
        $w = [
            "total_wallets_count" => $total_wallets
        ];

        // All Transactions
        $all_transactions = Transaction::all();
        $at = [
            "total_transactions_count" => $all_transactions->count(),
            "total_transactions_value_ngn" => round($all_transactions->sum('ngn_value'), 2),
            "total_transactions_value_usd" => round($all_transactions->sum('usd_value'), 2)
        ];

        // Pending Transactions
        $pending_transactions = Transaction::where('status', 'pending')->get();
        $pt = [
            "total_transactions_count" => $pending_transactions->count(),
            "total_transactions_value_ngn" => round($pending_transactions->sum('ngn_value'), 2),
            "total_transactions_value_usd" => round($pending_transactions->sum('usd_value'), 2)
        ];

        // Complete Transactions
        $complete_transactions = Transaction::where('status', 'complete')->get();
        $ct = [
            "total_transactions_count" => $complete_transactions->count(),
            "total_transactions_value_ngn" => round($complete_transactions->sum('ngn_value'), 2),
            "total_transactions_value_usd" => round($complete_transactions->sum('usd_value'), 2)
        ];

        // Transactions
        $t = [
            "all_transactions" => $at,
            "pending_transactions" => $pt,
            "complete_transactions" => $ct
        ];

        $analytics = [
            "users" => $u,
            "wallets" => $w,
            "transactions" => $t
        ];

        return $analytics;
    }
}
