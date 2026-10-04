<?php

namespace App\Http\Requests;

use App\Models\Bookmark;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookmarkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Anyone signed in may create a bookmark; only the owner may update one.
     */
    public function authorize(): bool
    {
        $bookmark = $this->route('bookmark');

        return ! $bookmark instanceof Bookmark || $this->user()->can('update', $bookmark);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:255'],
            'note' => ['nullable', 'string', 'max:65535'],
            'collection_id' => ['nullable', 'integer', Rule::exists('collections', 'id')->where('user_id', $this->user()->id)],
            'is_favorite' => ['boolean'],
            'tags' => ['array'],
            'tags.*' => ['integer', 'distinct', Rule::exists('tags', 'id')->where('user_id', $this->user()->id)],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_favorite' => $this->boolean('is_favorite'),
        ]);
    }
}
