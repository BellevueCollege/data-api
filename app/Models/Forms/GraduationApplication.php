<?php

namespace App\Models\Forms;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class GraduationApplication
{
    //
    public static function createRecord($sid,
                                        $received,
                                        $quarter,
                                        $programCode,
                                        $concentration,
                                        $diplomaName,
                                        $entryID,
                                        $isUpdate)
    {
        /*
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
                    . '@Email = :Email;';
        */
        $input_data = array( 
            'SID'           => $sid, 
            'Received'      => $received,
            'Quarter'       => $quarter,
            'ProgramCode'   => $programCode,
            'Concentration' => $concentration,
            'DiplomaName'   => $diplomaName,
            'EntryID'       => $entryID,
            'IsUpdate'      => $isUpdate,
        );
        
        // debug
        Log::debug('Database Write Disabled- Graduation Application: ' . print_r($input_data, true));

        /**
         * Write to Database
         */
        /*
        try
        {
            $update = DB::connection('pciforms')->update($tsql, $input_data);
        }
        catch(\Exception $error)
        {
            return response()->json([
                'message' => 'Database Error: ' . $error->getMessage()], 503);
        }
        */
    }
}
