<?php

namespace App\Services;

use App\Models\Manuscript;
use App\Models\ManuscriptContributor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ManuscriptService
{
    /**
     * Create a new manuscript with all related entities
     */
    public function createManuscript(array $data, Request $request): Manuscript
    {
        // Step 1: Create manuscript
        $user = $request->user();
        $manuscript = $user->manuscripts()->create($data);

        // Step 2: Sync contributors
        if (!empty($data['contributors'])) {
            $this->syncContributors($manuscript, $data['contributors']);
        }

        // Step 3: Handle file uploads
        if (!empty($data['manuscript_files'])) {
            $this->handleFiles($manuscript, $data['manuscript_files'], $request->user()->id);
        }

        // Step 4: Add copyright details
        $this->createCopyright($manuscript, $data);

        return $manuscript;
    }

    /**
     * Sync contributors to manuscript
     */
    protected function syncContributors(Manuscript $manuscript, array $contributors): void
    {
        foreach ($contributors as $contributorData) {
            $manuscript->contributors()->create([
                'user_id' => $contributorData['user_id'] ?? null,
                'first_name' => $contributorData['first_name'] ?? null,
                'last_name' => $contributorData['last_name'] ?? null,
                'email' => $contributorData['email'] ?? null,
                'affiliation' => $contributorData['affiliation'] ?? null,
                'country' => $contributorData['country'] ?? null,
                'is_principal' => $contributorData['is_principal'] ?? false,
                'order' => $contributorData['order'] ?? 1,
            ]);
        }
    }

    /**
     * Handle manuscript file uploads
     */
    protected function handleFiles(Manuscript $manuscript, array $files, int $uploadedBy): void
    {



        foreach ($files as $uploadedFile) {

            $oldPath = storage_path("app/public/{$uploadedFile['file_path']}");
            $newPath = "manuscript_files/" . basename($uploadedFile['file_path']);
            $fileExtension = pathinfo($uploadedFile['file_name'], PATHINFO_EXTENSION);
            if (file_exists($oldPath)) {
                Storage::disk('public')->move($uploadedFile['file_path'], $newPath);

                $manuscript->files()->create([
                    'uploaded_by' => $uploadedBy,
                    'file_name' => $uploadedFile['file_name'],
                    'file_path' => $newPath,
                    'file_type' => 'manuscript',
                    'file_extension' => $fileExtension,
                ]);
            }

            
        }
    }

    /**
     * Create copyright record
     */
    protected function createCopyright(Manuscript $manuscript, array $data): void
    {
        $copyrightData = $data['copyright'] ?? [];

        // Helper to move temp files
        $moveTempFile = function ($tempPath, $destinationFolder) {
            if ($tempPath && Storage::disk('public')->exists($tempPath)) {
                $fileName = basename($tempPath);
                $newPath = $destinationFolder . '/' . $fileName;
                Storage::disk('public')->move($tempPath, $newPath);
                return $newPath;
            }
            return null;
        };

        // Handle file uploads (now temporary paths)
        if (!empty($copyrightData['human_subjects_approval_file'])) {
            $copyrightData['human_subjects_approval_path'] = $moveTempFile(
                $copyrightData['human_subjects_approval_file'],
                'copyright_docs'
            );
        }
        if (!empty($copyrightData['animal_subjects_approval_file'])) {
            $copyrightData['animal_subjects_approval_path'] = $moveTempFile(
                $copyrightData['animal_subjects_approval_file'],
                'copyright_docs'
            );
        }
        if (!empty($copyrightData['us_government_permission_file'])) {
            $copyrightData['us_government_permission_path'] = $moveTempFile(
                $copyrightData['us_government_permission_file'],
                'copyright_docs'
            );
        }

        // Convert 'Yes'/'No' to boolean for database
        $booleanFields = [
            'is_corporate_interest',
            'has_human_subjects',
            'has_animal_subjects',
            'is_conflict_interest',
            'has_us_government_author',
            'used_ai_technology',
        ];

        foreach ($booleanFields as $field) {
            if (isset($copyrightData[$field])) {
                $copyrightData[$field] = $copyrightData[$field] === 'Yes';
            }
        }

        $manuscript->copyright()->create($copyrightData);
    }
}
