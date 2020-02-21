<?php

namespace App\Models\Forms;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class TransferCreditEvaluation
{
    //
    public static function createRecord(
        $sid,
        $received,
        $programCode,
        $military,
        $internationalTranscript,
        $entryID
    ) {

        $tsql = 'EXEC [usp_InsertIntoCreditReviews]'
                    . '@crSID = :crSID,'
                    . '@crReceived = :crReceived,'
                    . '@crProgramCode = :crProgramCode,'
                    . '@crMilitary = :crMilitary,'
                    . '@crInternationalTranscript = :crInternationalTranscript,'
                    . '@EntryID = :EntryID;';

        $input_data = array(
            'crSID'                     => $sid,
            'crReceived'                => $received,
            'crProgramCode'             => $programCode,
            'crMilitary'                => $military,
            'crInternationalTranscript' => $internationalTranscript,
            'EntryID'                   => $entryID,
        );

        /**
         * Write to Database
         */

        try {
            $update = DB::connection('evalforms')->update($tsql, $input_data);
        } catch (\Exception $error) {
            return response()->json([
                'message' => 'Database Error: ' . $error->getMessage()], 503);
        }
    }
}
