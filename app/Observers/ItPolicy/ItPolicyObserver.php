<?php

namespace App\Observers\ItPolicy;

use App\Models\ItPolicy;

class ItPolicyObserver
{
    /**
     * Handle the ItPolicy "created" event.
     *
     * @param  \App\Models\ItPolicy  $itPolicy
     * @return void
     */
    public function created(ItPolicy $itPolicy)
    {
        // Check if request has roles array
        if (request()->has('roles')) {

            // Cast $request->roles to $roles
            $roles = request()->roles;

            // Loop through roles array
            foreach ($roles as $role) {
                $itPolicy->roles()->attach($role);
            }
        }
    }

    /**
     * Handle the ItPolicy "updated" event.
     *
     * @param  \App\Models\ItPolicy  $itPolicy
     * @return void
     */
    public function updated(ItPolicy $itPolicy)
    {
        //
    }

    /**
     * Handle the ItPolicy "deleted" event.
     *
     * @param  \App\Models\ItPolicy  $itPolicy
     * @return void
     */
    public function deleted(ItPolicy $itPolicy)
    {
        //
    }

    /**
     * Handle the ItPolicy "restored" event.
     *
     * @param  \App\Models\ItPolicy  $itPolicy
     * @return void
     */
    public function restored(ItPolicy $itPolicy)
    {
        //
    }

    /**
     * Handle the ItPolicy "force deleted" event.
     *
     * @param  \App\Models\ItPolicy  $itPolicy
     * @return void
     */
    public function forceDeleted(ItPolicy $itPolicy)
    {
        //
    }
}