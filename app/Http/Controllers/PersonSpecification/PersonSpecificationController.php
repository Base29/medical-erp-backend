<?php

namespace App\Http\Controllers\PersonSpecification;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\PersonSpecification\CreatePersonSpecificationRequest;
use App\Http\Requests\PersonSpecification\FetchPersonSpecificationRequest;
use App\Models\PersonSpecification;
use App\Models\PersonSpecificationAttribute;
use App\Models\Practice;

class PersonSpecificationController extends Controller
{
    // Create person specification
    public function create(CreatePersonSpecificationRequest $request)
    {
        try {
            // Get practice
            $practice = Practice::findOrFail($request->practice);

            // Instance of PersonSpecification model
            $personSpecification = new PersonSpecification();
            $personSpecification->name = $request->name;
            // Save Person Specifications
            $practice->personSpecifications()->save($personSpecification);

            // Save attributes

            foreach ($request->person_attributes as $personAttribute) {
                // Instance of PersonSpecificationAttributes
                $personSpecificationAttribute = new PersonSpecificationAttribute();
                $personSpecificationAttribute->attribute = $personAttribute['attribute'];
                $personSpecificationAttribute->essential = $personAttribute['essential'];
                $personSpecificationAttribute->desirable = $personAttribute['desirable'];

                // Save person specification attribute
                $personSpecification->personSpecificationAttributes()->save($personSpecificationAttribute);
            }

            // Return success response
            return Response::success([
                'person-specification' => $personSpecification->with('personSpecificationAttributes', 'practice')
                    ->latest()
                    ->first(),
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch person specification
    public function fetch(FetchPersonSpecificationRequest $request)
    {
        try {
            // Get practice
            $practice = Practice::findOrFail($request->practice);

            // Get person specifications for $practice
            $personSpecifications = PersonSpecification::where('practice_id', $practice->id)
                ->with('personSpecificationAttributes', 'practice')
                ->latest()
                ->get();

            // Return success response
            return Response::success([
                'person-specifications' => $personSpecifications,
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}