<?php

namespace App\Http\Controllers\Offer;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Offer\CreateOfferRequest;
use App\Http\Requests\Offer\DeleteOfferRequest;
use App\Http\Requests\Offer\UpdateOfferRequest;
use App\Services\Offer\OfferService;

class OfferController extends Controller
{
    // Local variable
    protected $offerService;

    // Constructor
    public function __construct(OfferService $offerService)
    {
        // Inject Service
        $this->offerService = $offerService;
    }

    // Create
    public function create(CreateOfferRequest $request)
    {
        try {
            // Create offer service
            return $this->offerService->createOffer($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update
    public function update(UpdateOfferRequest $request)
    {
        try {
            // Update offer service
            return $this->offerService->updateOffer($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete
    public function delete(DeleteOfferRequest $request)
    {
        try {
            // Delete offer service
            return $this->offerService->deleteOffer($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}