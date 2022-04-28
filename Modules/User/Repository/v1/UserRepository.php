<?php

namespace Module\User\Repository\v1;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Module\Share\Repository\Repository;
use Module\User\Models\User;

class UserRepository implements Repository
{
    /**
     * Specify Model
     * Abstract function
     */
    public function model()
    {
        return User::query();
    }

    /**
    * Paginate $this->model
    * @param int $number
    * @return \Illuminate\Database\Eloquent\Model
    */
    public function paginate($number = 10)
    {
        if (Gate::denies('viewAny',User::class)){
            abort(403);
        }

//        return $this->model()->paginate($number);

        return $this->model()->withTrashed()->paginate($number);
    }

    /**
    * Show $this->model
    * @param int $id
    * @return \Module\User\Models\User
    */
    public function show($id)
    {
        /*if (Gate::denies('view', [User::class, $id])) {
            abort(403);
        }*/

        return $this->model()->findOrFail($id);
    }

    /**
     * Destroy User model
     *
     * @param  \Module\User\Models\User $user
     * @return boolean
     */
    public function destroy($user)
    {
        if (Gate::denies('delete', [User::class, $user])) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return $user->delete();
    }

    /**
     * Find Admin & Super users
     */
    public function admins()
    {
        return User::query()
            ->where('type','admin')
            ->orWhere('type','super')
            ->get();
    }
}