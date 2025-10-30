<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJournalRequest extends FormRequest
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
        $journalId = $this->route('journal')->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'issn' => ['nullable', 'string', 'max:255', 'unique:journals,issn,' . $journalId],
            'eissn' => ['nullable', 'string', 'max:255', 'unique:journals,eissn,' . $journalId],
            'abbreviation' => ['nullable', 'string', 'max:255', 'unique:journals,abbreviation,' . $journalId],
            'description' => ['nullable', 'string'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
