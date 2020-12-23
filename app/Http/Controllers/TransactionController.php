<?php

/**
 * Project: Payment Point
 * File: TransactionController.php
 * Date: 12/22/20
 * Time: 12:37 PM
 * @author: muhammadikhsan
 * @copyright: IGN &copy; 2020
 */


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Payment;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payments = DB::table('payments')
            ->select(
                'users.firstname',
                'users.lastname',
                'payments.order_id',
                'payments.amount',
                'payments.transaction_status',
                'payments.payment_type',
                'payments.created_at',
                'payments.id'
            )
            ->leftJoin('users', 'users.id', '=', 'payments.user_id')
            ->leftJoin('products', 'products.id', '=', 'payments.product_id')
            ->orderBy('payments.created_at', 'desc')
            ->paginate(15);

        return view('transaction.index', compact('payments'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $payment = Payment::find($id);

        return view('transaction.edit', compact('payment'));
    }

    public function update(Request $request, $id)
    {
        $payment = Payment::find($id);
        $payment->transaction_status = $request->paymentStatus;
        $payment->save();

        return redirect()->route('transaction')->with('success', 'Transaction successfully updated');
    }
}
