<?php
namespace App\Services\Signature;

use App\Helpers\Response;
use App\Models\Policy;
use App\Models\Signature;

class SignatureService
{
    // Create signature
    public function createSignature($request)
    {
        // Fetching policy and related signatures
        $policy = Policy::where('id', $request->policy_id)->with('signatures')->firstOrFail();

        // Checking if the current logged in user has already signed the policy
        $alreadySigned = $policy->signatures->contains('user_id', auth()->user()->id);

        //TODO: Commented the below code block for testing purpose. This should be un-commented in production
        // Returning response incase the policy is already signed by the current logged in user
        // if ($alreadySigned) {
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

        // Returning response if the policy is successfully signed by the currently logged in user
        return Response::success([
            'code' => Response::HTTP_CREATED,
            'signature' => $signature->with('policy')->latest()->firstOrFail(),
        ]);
    }

    // Fetch signature
    public function fetchSignatures()
    {
        // Get all signatures from DB
        $signatures = Signature::with('user', 'policy.practice')->latest()->paginate(10);

        return Response::success([
            'code' => Response::HTTP_OK,
            'signatures' => $signatures,
        ]);
    }
}