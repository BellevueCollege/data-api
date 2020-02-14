<?php

namespace App\Models\Forms;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class GraduationApplication
{
    //
    public static function createRecord($sid,
                                        $email,
                                        $received,
                                        $quarter,
                                        $programCode,
                                        $concentration,
                                        $diplomaName,
                                        $entryID,
                                        $isUpdate)
    {

        $tsql = 'EXEC [usp_InsertIntoGradApps]'
                    . '@grSID = :grSID,'
                    //. '@BCEmail = :BCEmail,' // Disabled as it is not needed at this time
                    . '@grReceived = :grReceived,'
                    . '@grQuarter = :grQuarter,'
                    . '@grProgramCode = :grProgramCode,'
                    . '@grConcentration = :grConcentration,'
                    . '@grDiplomaName = :grDiplomaName,'
                    . '@EntryID = :EntryID,'
                    . '@IsUpdate = :IsUpdate;';

        $input_data = array(
            'grSID'           => $sid,
            //'BCEmail'         => $email, // Disabled as it is not needed at this time
            'grReceived'      => $received,
            'grQuarter'       => $quarter,
            'grProgramCode'   => $programCode,
            'grConcentration' => $concentration,
            'grDiplomaName'   => $diplomaName,
            'EntryID'         => $entryID,
            'IsUpdate'        => $isUpdate,
        );

        /**
         * Write to Database
         */
        try
        {
            $update = DB::connection('evalforms')->update($tsql, $input_data);
        }
        catch(\Exception $error)
        {
            return response()->json([
                'message' => 'Database Error: ' . $error->getMessage()], 503);
        }

    }
}
