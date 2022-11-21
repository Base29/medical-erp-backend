<?php
namespace App\Services\PersonSpecification;

use App\Helpers\Response;
use App\Models\PersonSpecification;
use App\Models\PersonSpecificationAttribute;
use App\Models\Practice;

class PersonSpecificationService
{
    // Create person specification
    public function createPersonSpecification($request)
    {
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
            'code' => Response::HTTP_CREATED,
            'person-specification' => $personSpecification->with('personSpecificationAttributes', 'practice')
                ->latest()
                ->first(),
        ]);
    }

    // Fetch person specifications
    public function fetchPersonSpecifications($request)
    {
        // Get practice
        $practice = Practice::findOrFail($request->practice);

        // Get person specifications for $practice
        $personSpecifications = PersonSpecification::where('practice_id', $practice->id)
            ->with('personSpecificationAttributes', 'practice')
            ->latest()
            ->get();

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'person-specifications' => $personSpecifications,
        ]);
    }

    // Delete Person specification
    public function deletePersonSpecification($request)
    {
        // Get person specification
        $personSpecification = PersonSpecification::findOrFail($request->person_specification);

        // Delete person specification
        $personSpecification->delete();

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'person-specification' => $personSpecification,
        ]);
    }

    // Fetch single person specification
    public function fetchSinglePersonSpecification($request)
    {
        // Get job specification
        $personSpecification = PersonSpecification::where('id', $request->person_specification)
            ->with('practice', 'personSpecificationAttributes')
            ->firstOrFail();

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'person-specification' => $personSpecification,
        ]);
    }
}