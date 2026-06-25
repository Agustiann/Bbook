<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\BookCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookCategoryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:BookCategory');
    }

    public function view(AuthUser $authUser, BookCategory $bookCategory): bool
    {
        return $authUser->can('View:BookCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:BookCategory');
    }

    public function update(AuthUser $authUser, BookCategory $bookCategory): bool
    {
        return $authUser->can('Update:BookCategory');
    }

    public function delete(AuthUser $authUser, BookCategory $bookCategory): bool
    {
        return $authUser->can('Delete:BookCategory');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:BookCategory');
    }

    public function restore(AuthUser $authUser, BookCategory $bookCategory): bool
    {
        return $authUser->can('Restore:BookCategory');
    }

    public function forceDelete(AuthUser $authUser, BookCategory $bookCategory): bool
    {
        return $authUser->can('ForceDelete:BookCategory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:BookCategory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:BookCategory');
    }

    public function replicate(AuthUser $authUser, BookCategory $bookCategory): bool
    {
        return $authUser->can('Replicate:BookCategory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:BookCategory');
    }

}