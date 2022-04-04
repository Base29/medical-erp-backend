<?php

namespace App\Http\Controllers\Answer;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Answer\CreateAnswerRequest;
use App\Http\Requests\Answer\FetchAnswersRequest;
use App\Http\Requests\Answer\UpdateAnswerRequest;
use App\Services\Answer\AnswerService;

class AnswerController extends Controller
{
    // Local variable
    protected $answerService;

    // Constructor
    public function __construct(AnswerService $answerService)
    {
        $this->answerService = $answerService;
    }

    // Create
    public function create(CreateAnswerRequest $request)
    {
        try {

            // Create answer service
            $answer = $this->answerService->createAnswer($request);

            // Return response
            return Response::success(['answer' => $answer->with('user')->latest('id')->first()]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // fetch post answers
    public function fetch(FetchAnswersRequest $request)
    {
        try {
            // Fetch answers service
            $answers = $this->answerService->fetchAllAnswers($request);

            // Return success response
            return Response::success(['post_answers' => $answers]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update answer
    public function update(UpdateAnswerRequest $request)
    {

        try {
            // Update answer service
            $answer = $this->answerService->updateAnswer($request);

            // Return success response
            return Response::success([
                'answer' => $answer,
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function delete($id)
    {
        try {

            // Delete answer service
            $this->answerService->deleteAnswer($id);

            // Return success response
            return Response::success(['message' => ResponseMessage::deleteSuccess('Answer')]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}