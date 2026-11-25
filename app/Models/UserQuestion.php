<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class UserQuestion
{
    /**
     * Write User Question to Database. Return any errors.
     */
    public static function createUserQuestionRecord(/*$createDT,*/
                                            $question)
    {
        $tsql = 'EXEC [usp_InsertUserQuestions]'
//                    . '@CreateDT = :CreateDT,'
                    . '@Question = :Question;';

        $input_data = array(
//            'CreateDT'     => $createDT,
            'Question' => $question,
        );

        /**
         * Write to Database
         */
        try
        {
            $update = DB::connection('copilot')->update($tsql, $input_data);
        }
        catch(\Exception $error)
        {
            Log::warning('Copilot user question write failed', [
                'connection' => 'copilot',
                'message' => $error->getMessage(),
            ]);

            return response()->json([
                'message' => 'Database Error',
            ], 503);
        }
        return response()->json([
            'message' => 'Transaction successfully written to database.'], 200);

    }
}
