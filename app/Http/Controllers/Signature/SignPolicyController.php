<?php

namespace App\Http\Controllers\Signature;

use App\Helpers\CustomValidation;
use App\Http\Controllers\Controller;
use App\Models\Policy;
use App\Models\Signature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SignPolicyController extends Controller
{
    public function __invoke(Request $request)
    {
        // Validation rules
        $rules = [
            'confirmation' => 'required|boolean',
            'policy_id' => 'required|numeric',
        ];

        // Validating params in request
        $validator = Validator::make($request->all(), $rules);

        // If validation fails
        if ($validator->fails()) {
            // Return error messages against $rules
            return CustomValidation::error_messages($rules, $validator);
        }

        // Fetching policy and related signatures
        $policy = Policy::where('id', $request->policy_id)->with('signatures')->first();

        // Returning response incase the policy with the provided Id is not available
        if (!$policy) {
            return response([
                'success' => false,
                'message' => 'Something went wrong while fetching policy with id ' . $request->policy_id,
            ]);
        }

        // Checking if the current logged in user has already signed the policy
        $already_signed = $policy->signatures->contains('user_id', auth()->user()->id);

        //TODO: Commented the below code block for testing purpose. This should be removed in production
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

        // Returning response incase something went wrong while creating the signature
        if (!$signature) {
            return response([
                'success' => false,
                'message' => 'Something went wrong while creating signature',
            ]);
        }

        // Returning response if the policy is successfully sgined by the currently logged in user
        return response([
            'success' => true,
            'message' => $policy->name . ' has been signed by ' . auth()->user()->name,
        ]);
    }
}