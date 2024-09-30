<?php

namespace App\Observers;

use App\Mail\UserRegisteredEmail;
use App\Models\User;
use App\Notifications\UserRegistered;
use Illuminate\Support\Facades\Mail;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        if ($user->role_id != 1) {
            $superAdmins = User::where('role_id', 1)->get();
            foreach ($superAdmins as $superAdmin) {
                $superAdmin->notify(new UserRegistered($user));
                Mail::to($superAdmin->email)->send(new UserRegisteredEmail($user));
            }
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
