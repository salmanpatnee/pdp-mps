<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreManuscriptRequest;
use App\Http\Resources\ManuscriptResource;
use App\Models\Manuscript;
use App\Models\ManuscriptFile;
use App\Services\ManuscriptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter; // Added
use Illuminate\Database\Eloquent\Builder; // Added
use App\Events\ManuscriptSubmitted; // Added

class ManuscriptController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Reference number format: PDP-JJJJ-YYYY-NNNN
        $referenceNoPattern = '/^PDP-[A-Z]{4}-\d{4}-\d{4}$/i';

        return ManuscriptResource::collection(QueryBuilder::for(Manuscript::class)->with('journal')
            ->allowedFilters([
                'title',
                'reference_no',
                AllowedFilter::callback('search', function (Builder $query, $value) use ($referenceNoPattern) {
                    if (preg_match($referenceNoPattern, $value)) {
                        // Prioritize search by reference_no if it matches the pattern
                        $query->where('reference_no', 'LIKE', '%' . $value . '%');
                    } else {
                        // Search by both title and reference_no
                        $query->where(function ($q) use ($value) {
                            $q->where('title', 'LIKE', '%' . $value . '%')
                                ->orWhere('reference_no', 'LIKE', '%' . $value . '%');
                        });
                    }
                }),
            ])
            ->allowedSorts('title')
            ->paginate()
            ->appends(request()->query()));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreManuscriptRequest $request, ManuscriptService $service)
    {
        $manuscript = $service->createManuscript($request->validated(), $request);

        // event(new ManuscriptSubmitted($manuscript));
        
        ManuscriptSubmitted::dispatch($manuscript);

        return $this->ok('Manuscript added successfully.', new ManuscriptResource($manuscript));
    }

    /**
     * Display the specified resource.
     */
    public function show(Manuscript $manuscript)
    {
        $manuscript->load(['journal', 'author', 'contributors', 'articleType', 'copyright', 'files']);
        return new ManuscriptResource($manuscript);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreManuscriptRequest $request, Manuscript $manuscript)
    {
        $manuscript->update($request->validated());

        return $this->ok('Manuscript updated successfully.', new ManuscriptResource($manuscript));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Manuscript $manuscript)
    {
        $manuscript->delete();

        return $this->ok('');
    }

    public function uploadTemp(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:51200'],
        ]);

        $path = $request->file('file')->store('temp/manuscripts', 'public');

        return response()->json([
            'file_path' => $path,
            'file_name' => $request->file('file')->getClientOriginalName(),
        ]);
    }

    public function downloadFile(ManuscriptFile $manuscriptFile)
    {
        // Check if file exists in storage
        if (!Storage::disk('public')->exists($manuscriptFile->file_path)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        // Get the file path and original name
        $filePath = $manuscriptFile->file_path;
        $fileName = $manuscriptFile->file_name;

        // Return file download response
        return response()->download(Storage::disk('public')->path($filePath), $fileName);
    }
}
