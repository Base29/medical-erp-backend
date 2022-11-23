<?php

namespace App\Http\Controllers\Offer;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Offer\CreateOfferRequest;
use App\Http\Requests\Offer\DeleteOfferRequest;
use App\Http\Requests\Offer\FetchSingleOfferRequest;
use App\Http\Requests\Offer\OfferAmendmentRequest;
use App\Http\Requests\Offer\UpdateOfferAmendmentRequest;
use App\Http\Requests\Offer\UpdateOfferRequest;
use App\Services\Offer\OfferService;
use Exception;

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

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
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

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
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

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch single
    public function fetchSingle(FetchSingleOfferRequest $request)
    {
        try {
            // Fetch single offer service
            return $this->offerService->fetchSingleOffer($request);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Amend Offer
    public function amendOffer(OfferAmendmentRequest $request)
    {
        try {
            // Logic here
            return $this->offerService->amendHiringRequestOffer($request);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update amendment
    public function updateAmendment(UpdateOfferAmendmentRequest $request)
    {
        try {
            // Logic here
            return $this->offerService->updateOfferAmendment($request);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }
}