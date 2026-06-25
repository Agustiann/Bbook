<?php

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class BorrowerPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Borrower');
    }

    public function view(AuthUser $authUser): bool
    {
        return $authUser->can('View:Borrower');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Borrower');
    }

    public function update(AuthUser $authUser): bool
    {
        return $authUser->can('Update:Borrower');
    }

    public function delete(AuthUser $authUser): bool
    {
        return $authUser->can('Delete:Borrower');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Borrower');
    }

    public function restore(AuthUser $authUser): bool
    {
        return $authUser->can('Restore:Borrower');
    }

    public function forceDelete(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDelete:Borrower');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Borrower');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Borrower');
    }

    public function replicate(AuthUser $authUser): bool
    {
        return $authUser->can('Replicate:Borrower');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Borrower');
    }

}