<?php

namespace App\Http\Controllers;

use App\Models\UserQuestion;
use Illuminate\Http\Request;

class UserQuestionController extends Controller
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
    public function getUserQuestions(Request $request)
    {
        return [
            '🛑🙅 - Posts only, please! 👋'
        ];
    }

    /**
     * Write user question to database
     */
    public function postUserQuestion(Request $request)
    {

        /**
         * Validate Request
         */
        $this->validate($request, [
//            'createDT'        => 'string|max:20|nullable',
            'question'        => 'string|max:4000|nullable',
        ]);

        return UserQuestion::createUserQuestionRecord(
  //          $request->input('createDT', null),
            $request->input('question', null)
        );
    }

    public function postTestUserQuestion(Request $request)
    {
        /**
         * Validate Request
         */
        $this->validate($request, [
//            'createDT'          => 'string|max:20|nullable',
            'question'          => 'string|max:4000|nullable',
        ]);

        return array(
            'NOTHING ACTUALLY WRITTEN TO DATABASE!',
            'data'              => $request->all(),
        );
    }
    //
}
