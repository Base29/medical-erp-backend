<?php

namespace App\Http\Controllers\Signature;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Models\Signature;
use App\Services\Signature\SignatureService;
use Illuminate\Http\Request;

class SignatureController extends Controller
{
    // Local variable
    protected $signatureService;

    // Constructor
    public function __construct(SignatureService $signatureService)
    {
        // Inject Service
        $this->signatureService = $signatureService;
    }

    public function signPolicy(Request $request)
    {
        try {

            // Sign policy
            return $this->signatureService->createSignature($request);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch all signatures
    public function fetch()
    {
        try {

            // Fetch signatures
            return $this->signatureService->fetchSignatures();

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}