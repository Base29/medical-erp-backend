<?php

namespace App\Http\Controllers\InductionResult;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\InductionResult\CreateInductionResultRequest;

class InductionResultController extends Controller
{
    // Create induction result
    public function create(CreateInductionResultRequest $request)
    {
        try {
            // Logic here
        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}