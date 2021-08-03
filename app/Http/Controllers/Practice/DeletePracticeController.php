<?php

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Models\Practice;

class DeletePracticeController extends Controller
{
    public function __invoke($id)
    {
        // Check if practice exists
        $practice = Practice::find($id);

        if (!$practice) {
            return response([
                'success' => false,
                'message' => 'Practice with the provided id ' . $id . ' doesn\'t exists',
            ], 404);
        }

        // Deleting practice
        $practice->delete();

        return response([
            'success' => true,
            'message' => 'Practice deleted successfully',
        ], 200);

    }
}