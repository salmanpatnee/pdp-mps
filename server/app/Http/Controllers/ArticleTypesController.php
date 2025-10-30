<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleTypeRequest;
use App\Http\Resources\ArticleTypeResource;
use App\Models\ArticleType;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class ArticleTypesController extends ApiController
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ArticleTypeResource::collection(QueryBuilder::for(ArticleType::class)
            ->allowedFilters(['name'])
            ->allowedSorts('name')
            ->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArticleTypeRequest $request) {}

    /**
     * Display the specified resource.
     */
    public function show(ArticleType $articleType)
    {
        return ArticleTypeResource::make($articleType);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ArticleType $articleType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ArticleType $articleType)
    {
        //
    }
}
