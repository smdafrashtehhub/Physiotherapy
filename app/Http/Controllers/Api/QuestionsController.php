<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuestionRequest;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionsController extends Controller
{
    public function  editUser()
    {
        return response()->json([
           'status'=>true,
           'message'=>Question::select('question')->get()
        ]);
    }
    public function create(QuestionRequest $request)
    {
        Question::create([
           'question'=>$request->question
        ]);
        return response()->json([
            'status'=>true,
            'message'=>'question saved'
        ]);
    }
}
