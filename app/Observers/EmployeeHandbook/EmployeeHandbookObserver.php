<?php

namespace App\Observers\EmployeeHandbook;

use App\Models\EmployeeHandbook;

class EmployeeHandbookObserver
{
    /**
     * Handle the EmployeeHandbook "created" event.
     *
     * @param  \App\Models\EmployeeHandbook  $employeeHandbook
     * @return void
     */
    public function created(EmployeeHandbook $employeeHandbook)
    {
        // Check if request has roles array
        if (request()->has('roles')) {

            // Cast $request->roles to $roles
            $roles = request()->roles;

            // Loop through roles array
            foreach ($roles as $role) {
                $employeeHandbook->roles()->attach($role);
            }
        }
    }

    /**
     * Handle the EmployeeHandbook "updated" event.
     *
     * @param  \App\Models\EmployeeHandbook  $employeeHandbook
     * @return void
     */
    public function updated(EmployeeHandbook $employeeHandbook)
    {
        //
    }

    /**
     * Handle the EmployeeHandbook "deleted" event.
     *
     * @param  \App\Models\EmployeeHandbook  $employeeHandbook
     * @return void
     */
    public function deleted(EmployeeHandbook $employeeHandbook)
    {
        //
    }

    /**
     * Handle the EmployeeHandbook "restored" event.
     *
     * @param  \App\Models\EmployeeHandbook  $employeeHandbook
     * @return void
     */
    public function restored(EmployeeHandbook $employeeHandbook)
    {
        //
    }

    /**
     * Handle the EmployeeHandbook "force deleted" event.
     *
     * @param  \App\Models\EmployeeHandbook  $employeeHandbook
     * @return void
     */
    public function forceDeleted(EmployeeHandbook $employeeHandbook)
    {
        //
    }
}