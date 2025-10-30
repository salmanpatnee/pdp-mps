<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class UsersController extends ApiController
{
    use ApiResponses;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if ($this->isAble('viewAny', User::class)) {
            return UserResource::collection(
                QueryBuilder::for(User::class)
                    ->where('role_id', '!=', 1) // Excluding superadmin
                    ->allowedFilters(['name', 'email', 'country', 'affiliation', 'profile_link'])
                    ->allowedSorts('name', 'id')
                    ->paginate()
                    ->appends(request()->query())
            );
        }

        return $this->error('You are not authorized to view users', 401);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        if ($this->isAble('create', User::class)) {
            $user = User::create($request->validated());

            return $this->ok('User registered successfully.', new UserResource($user));
        }

        return $this->error('You are not authorized to create user', 401);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        if ($this->isAble('view', User::class)) {
            return new UserResource($user);
        }

        return $this->error('You are not authorized to view user', 401);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        if ($this->isAble('update', $user)) {

            $data = $request->validated();
            if (empty($data['password'])) {
                unset($data['password']);
            }
            $user->update($data);

            return $this->ok('User updated successfully.', new UserResource($user));
        }

        return $this->error('You are not authorized to create user', 401);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($this->isAble('delete', $user)) {

            $user->delete();

            return $this->ok('User deleted successfully.');
        }
        return $this->error('You are not authorized to delete this journal', 401);
    }

    /**
     * Search for a user by email.
     */
    public function search(Request $request)
    {
        if ($this->isAble('viewAny', User::class)) {
            $email = $request->query('email');

            if (!$email) {
                return $this->error('Email query parameter is required.', 400);
            }

            $user = User::where('email', $email)->first();

            if ($user) {
                return new UserResource($user);
            }

            return $this->error('Author not found.', 404);
        }

        return $this->error('You are not authorized to search for users.', 401);
    }
}
