<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJournalRequest;
use App\Http\Requests\UpdateJournalRequest;
use App\Http\Resources\JournalResource;
use App\Models\Journal;
use Spatie\QueryBuilder\QueryBuilder;

class JournalsController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return JournalResource::collection(QueryBuilder::for(Journal::class)
            ->allowedFilters(['name', 'issn', 'eissn', 'abbreviation', 'description', 'publisher', 'email', 'website_url'])
            ->allowedSorts('name', 'id')
            ->paginate()
            ->appends(request()->query()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJournalRequest $request)
    {

        if ($this->isAble('create', Journal::class)) {
            $journal = Journal::create($request->validated());
            return $this->ok('Journal added successfully.', new JournalResource($journal));
        }

        return $this->error('You are not authorized to create journal', 401);
    }

    /**
     * Display the specified resource.
     */
    public function show(Journal $journal)
    {
        return new JournalResource($journal);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJournalRequest $request, Journal $journal)
    {
        if ($this->isAble('update', $journal)) {
            $journal->update($request->validated());

            return $this->ok('Journal updated successfully.', new JournalResource($journal));
        }

        return $this->error('You are not authorized to create journal', 401);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Journal $journal)
    {
        if ($this->isAble('delete', $journal)) {

            $journal->delete();

            return $this->ok('');
        }
        return $this->error('You are not authorized to delete this journal', 401);
    }
}
