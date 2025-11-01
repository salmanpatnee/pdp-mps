<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreManuscriptRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'journal_id' => ['required', 'integer', 'exists:journals,id'],
            // 'user_id' => ['required', 'integer', 'exists:users,id'],
            'article_type_id' => ['required', 'integer', 'exists:article_types,id'],
            'submission_type' => ['required', 'in:manuscript,proposed_abstract,thematic_issue'],
            'title' => ['required', 'string', 'max:255'],
            'abstract' => ['nullable', 'string'],
            'keywords' => ['nullable', 'string'],
            'status' => ['nullable', 'in:Awaiting Editorial Approval,submitted,under_review,revision,accepted,rejected'],
            'contributors' => ['nullable', 'array'],
            // 'contributors.*.user_id' => ['nullable', 'integer', 'exists:users,id'],
            'contributors.*.user_id' => ['nullable', 'integer'],
            'contributors.*.first_name' => ['required_without:contributors.*.user_id', 'string', 'max:255'],
            // 'contributors.*.last_name' => ['required_without:contributors.*.user_id', 'string', 'max:255'],
            'contributors.*.email' => ['nullable', 'email', 'max:255'],
            'contributors.*.affiliation' => ['nullable', 'string', 'max:255'],
            'contributors.*.country' => ['nullable', 'string', 'max:255'],
            'contributors.*.is_principal' => ['required', 'boolean'],
            'contributors.*.order' => ['required', 'integer'],
            'is_authorship_confirmed' => ['required', 'boolean', 'accepted'],
            'manuscript_files' => ['nullable', 'array'],
            'manuscript_files.*.file_name' => ['required', 'string'],
            'manuscript_files.*.file_path' => ['required', 'string'],
            'copyright.is_corporate_interest' => ['nullable', 'in:Yes,No'],
            'copyright.has_human_subjects' => ['nullable', 'in:Yes,No'],
            'copyright.has_animal_subjects' => ['nullable', 'in:Yes,No'],
            'copyright.is_conflict_interest' => ['nullable', 'in:Yes,No'],
            'copyright.has_us_government_author' => ['nullable', 'in:Yes,No'],
            'copyright.used_ai_technology' => ['nullable', 'in:Yes,No'],

            'copyright.human_subjects_approval_file' => ['nullable', 'required_if:copyright.has_human_subjects,Yes', 'string', 'max:255'],
            'copyright.animal_subjects_approval_file' => ['nullable', 'required_if:copyright.has_animal_subjects,Yes', 'string', 'max:255'],
            'copyright.conflict_of_interest_details' => ['nullable', 'required_if:copyright.is_conflict_interest,Yes', 'string', 'max:5000'],
            'copyright.us_government_permission_file' => ['nullable', 'required_if:copyright.has_us_government_author,Yes', 'string', 'max:255'],
            'copyright.ai_usage_details' => ['nullable', 'required_if:copyright.used_ai_technology,Yes', 'string', 'max:2000'],
        ];
    }
}
