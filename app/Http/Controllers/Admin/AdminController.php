<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $month = 12;

        $successTransactions = Transaction::getData($month, 1);
        $successTransactionsChart = $this->chart($successTransactions, $month);

        $unSuccessTransactions = Transaction::getData($month, 0);
        $unSuccessTransactionsChart = $this->chart($unSuccessTransactions, $month);


        //dd($successTransactionsChart, $unSuccessTransactionsChart);

        // dd($successTransactions, $unSuccessTransactions);
        return view('admin.dashboard', [
            'successTransactions' => array_values($successTransactionsChart),
            'unSuccessTransactions' => array_values($unSuccessTransactionsChart),
            'labels' => array_keys($successTransactionsChart),
            'transactionsCount' => [$successTransactions->count(), $unSuccessTransactions->count()],
        ]);
    }

    public function chart($transactions, $month)
    {
        $monthName = $transactions->map(function ($transaction) {
            return verta($transaction->created_at)->format('%B %y');
        });

        $amount = $transactions->map(function ($transaction) {
            return $transaction->amount;
        });

        $result = [];
        foreach ($monthName as $i => $v) {
            if (!isset($result[$v])) {
                $result[$v] = 0;
            }
            $result[$v] += $amount[$i];
        }
        if (count($result) != $month) {
            for ($i = 0; $i < $month; $i++) {
                $monthName = verta()->subMonths($i)->format('%B %y');
                $shamsiMonths[$monthName] = 0;
            }
            return array_merge($shamsiMonths, $result);
        }
        return $result;
    }
}
