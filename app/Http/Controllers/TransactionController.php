<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Hello World for get requests
     */
    public function getTransactions(Request $request)
    {
        return [
            '🛑🙅 - Posts only, please! 👋'
        ];
    }

    /**
     * Write transaction to database
     */
    public function postTransaction(Request $request)
    {

        /**
         * Validate Request
         */
        $this->validate($request, [
            'id'                => 'required|numeric|max:9999999999999',
            'status'            => 'string|max:50|nullable',
            'settlement_date'   => 'date|nullable',
            'amount'            => 'numeric|nullable',
            'form_id'           => 'required|numeric|max:9999',
            'first_name'        => 'string|max:50|nullable',
            'last_name'         => 'string|max:50|nullable',
            'billing_address_1' => 'string|max:50|nullable',
            'billing_address_2' => 'string|max:50|nullable',
            'billing_city'      => 'string|max:50|nullable',
            'billing_state'     => 'string|max:50|nullable',
            'billing_zip'       => 'string|max:50|nullable',
            'sid'               => 'digits:9|nullable',
            'emplid'            => 'digits:9|nullable',
            'email'             => 'string|max:50|nullable',
        ]);

        return Transaction::createTransactionRecord(
            $request->input('id', null),
            $request->input('status', null),
            $request->input('settlement_date', null),
            $request->input('amount', null),
            $request->input('form_id', null),
            $request->input('first_name', null),
            $request->input('last_name', null),
            $request->input('billing_address_1', null),
            $request->input('billing_address_2', null),
            $request->input('billing_city', null),
            $request->input('billing_state', null),
            $request->input('billing_zip', null),
            $request->input('sid', null),
            $request->input('emplid', null),
            $request->input('email', null)
        );
    }

    public function postTestTransaction(Request $request)
    {
        /**
         * Validate Request
         */
        $this->validate($request, [
            'id'                => 'required|numeric|max:9999999999999',
            'status'            => 'string|max:50|nullable',
            'settlement_date'   => 'date|nullable',
            'amount'            => 'numeric|nullable',
            'form_id'           => 'required|numeric|max:9999',
            'first_name'        => 'string|max:50|nullable',
            'last_name'         => 'string|max:50|nullable',
            'billing_address_1' => 'string|max:50|nullable',
            'billing_address_2' => 'string|max:50|nullable',
            'billing_city'      => 'string|max:50|nullable',
            'billing_state'     => 'string|max:50|nullable',
            'billing_zip'       => 'string|max:50|nullable',
            'sid'               => 'digits:9|nullable',
            'emplid'            => 'digits:9|nullable',
            'email'             => 'string|max:50|nullable',
        ]);

        return array(
            'NOTHING ACTUALLY WRITTEN TO DATABASE!',
            'data'              => $request->all(),
        );
    }
    //
}
