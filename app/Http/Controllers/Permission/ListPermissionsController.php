<?php

namespace App\Http\Controllers\Permission;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class ListPermissionsController extends Controller
{
    public function __invoke()
    {
        // Fetching permissions
        $permissions = Permission::paginate(10);

        return response([
            'success' => true,
            'permissions' => $permissions,
        ]);
    }
}