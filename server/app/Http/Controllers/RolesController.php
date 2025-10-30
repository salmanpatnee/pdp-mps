<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class RolesController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if ($this->isAble('viewAny', Role::class)) {
            return RoleResource::collection(
                QueryBuilder::for(Role::class)
                    ->allowedFilters(['name'])
                    ->allowedSorts('name', 'id')
                    ->get()
            );
        }

        return $this->error('You are not authorized to view roles', 401);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        if ($this->isAble('view', $role)) {
            return new RoleResource($role);
        }

        return $this->error('You are not authorized to view role', 401);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
