<?php

namespace App\Http\Controllers\Signature;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Models\Policy;
use App\Models\Signature;
use Illuminate\Http\Request;

class SignatureController extends Controller
{
    public function signPolicy(Request $request)
    {
        try {

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

            // Returning response if the policy is successfully sgined by the currently logged in user
            return Response::success(['signature' => $signature->with('policy')->latest()->firstOrFail()]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch all signatures
    public function fetch()
    {
        try {
            // Get all signatures from DB
            $signatures = Signature::with('user', 'policy.practice')->latest()->paginate(10);

            return Response::success([
                'signatures' => $signatures,
            ]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
}