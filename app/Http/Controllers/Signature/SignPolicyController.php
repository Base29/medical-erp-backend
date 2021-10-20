<?php

namespace App\Http\Controllers\Signature;

use App\Helpers\CustomValidation;
use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Models\Policy;
use App\Models\Signature;
use Illuminate\Http\Request;

class SignPolicyController extends Controller
{
    public function __invoke(Request $request)
    {
        // Validation rules
        $rules = [
            'confirmation' => 'required|boolean',
            'policy_id' => 'required|numeric',
        ];

        // Validation errors
        $request_errors = CustomValidation::validate_request($rules, $request);

        // Return errors
        if ($request_errors) {
            return $request_errors;
        }

        // Fetching policy and related signatures
        $policy = Policy::where('id', $request->policy_id)->with('signatures')->first();

        // Returning response incase the policy with the provided Id is not available
        if (!$policy) {
            return Response::fail([
                'message' => ResponseMessage::notFound('Policy', $request->policy_id, false),
                'code' => 404,
            ]);
        }

        // Checking if the current logged in user has already signed the policy
        $already_signed = $policy->signatures->contains('user_id', auth()->user()->id);

        //TODO: Commented the below code block for testing purpose. This should be un-commented in production
        // Returning response incase the policy is already signed by the current logged in user
        // if ($already_signed) {
        //     return response([
        //         'success' => false,
        //         'message' => auth()->user()->name . ' has already signed ' . $policy->name,
        //     ]);
        // }

        // Creating signature of the current logged in user
        $signature = new Signature();
        $signature->comment = $request->comment;
        $signature->confirmation = $request->confirmation;
        $signature->user_id = auth()->user()->id;
        $signature->policy_id = $request->policy_id;
        $signature->save();

        // Returning response if the policy is successfully sgined by the currently logged in user
        return Response::success(['signature' => $signature->with('policy')->latest()->first()]);
    }
}