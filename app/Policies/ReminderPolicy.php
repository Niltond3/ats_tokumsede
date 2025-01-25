<?php

namespace App\Policies;

use App\Models\Administrador;
use App\Models\Reminder;
use Illuminate\Auth\Access\Response;

class ReminderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Administrador $administrador): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Administrador $administrador, Reminder $reminder): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Administrador $administrador): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Administrador $administrador, Reminder $reminder): bool
    {
        return true; // Or your specific authorization logic
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Administrador $administrador, Reminder $reminder): bool
{
    return true; // Or your specific authorization logic
}

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Administrador $administrador, Reminder $reminder): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Administrador $administrador, Reminder $reminder): bool
    {
        //
    }
}
