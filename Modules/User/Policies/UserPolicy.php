<?php

namespace Module\User\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Module\Role\Models\Role;
use Module\User\Models\User;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return
     */
    public function viewAny(User $user)
    {
        return $user->hasRole('writer');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param int $author
     * @return Response|bool
     */
    public function view(User $user, int $author)
    {
        return $user->id === $author;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param int $model
     * @return bool
     */
    public function update(User $user, int $model): bool
    {
        return $user->id === $model;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function delete(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }
}
