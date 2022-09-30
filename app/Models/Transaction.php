<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class Transaction
{
    /**
     * Write Transaction to Database. Return any errors.
     */
    public static function createTransactionRecord($id,
                                            $status,
                                            $settlementTime,
                                            $amount,
                                            $formID,
                                            $firstName,
                                            $lastName,
                                            $billingAddress1,
                                            $billingAddress2,
                                            $billingCity,
                                            $billingState,
                                            $billingZip,
                                            $sid,
                                            $emplid,
                                            $email)
    {
        $tsql = 'EXEC [usp_InsertTransactionDetails]'
                    . '@TranStatus = :TransactionStatus,'
                    . '@SettleTime = :SettlementTime,'
                    . '@TransID = :TransactionID,'
                    . '@Fname = :FirstName,'
                    . '@Lname = :LastName,'
                    . '@BillStreet1 = :BillingStreet1,'
                    . '@BillStreet2 = :BillingStreet2,'
                    . '@City = :City,'
                    . '@State = :State,'
                    . '@Zip = :Zip,'
                    . '@PayAmt = :PaymentAmount,'
                    . '@FormID = :FormID,'
                    . '@SID = :SID,'
                    . '@EMPLID = :EMPLID,'
                    . '@Email = :Email;';

        $input_data = array(
            'TransactionID'     => $id,
            'TransactionStatus' => $status,
            'SettlementTime'    => $settlementTime,
            'PaymentAmount'     => $amount,
            'FormID'            => $formID,
            'FirstName'         => $firstName,
            'LastName'          => $lastName,
            'BillingStreet1'    => $billingAddress1,
            'BillingStreet2'    => $billingAddress2,
            'City'              => $billingCity,
            'State'             => $billingState,
            'Zip'               => $billingZip,
            'SID'               => $sid,
            'EMPLID'            => $emplid,
            'Email'             => $email,
        );

        /**
         * Write to Database
         */
        try
        {
            $update = DB::connection('pciforms')->update($tsql, $input_data);
        }
        catch(\Exception $error)
        {
            return response()->json([
                'message' => 'Database Error: ' . $error->getMessage()], 503);
        }
        return response()->json([
            'message' => 'Transaction successfully written to database.'], 200);

    }
}
