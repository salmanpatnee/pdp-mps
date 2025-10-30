<?php

namespace App\Http\Resources;

use App\Http\Resources\ManuscriptContributorResource;
use App\Http\Resources\ManuscriptFileResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ManuscriptResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reference_no' => $this->reference_no,
            'journal_id' => $this->journal_id,
            'author_id' => $this->user_id,
            'article_type_id' => $this->article_type_id,
            'journal' => new JournalResource($this->whenLoaded('journal')),
            'author' => new UserResource($this->whenLoaded('author')),
            'contributors' => ManuscriptContributorResource::collection($this->whenLoaded('contributors')),
            'files' => ManuscriptFileResource::collection($this->whenLoaded('files')),
            'article_type' => new ArticleTypeResource($this->whenLoaded('articleType')),
            'submission_type' => $this->submission_type,
            'title' => $this->title,
            'abstract' => $this->abstract,
            'keywords' => $this->keywords,
            'status' => $this->status,
            'copyright' => new CopyrightResource($this->whenLoaded('copyright')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
