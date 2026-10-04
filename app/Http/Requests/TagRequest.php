<?php

namespace App\Http\Requests;

use App\Models\Tag;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TagRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Anyone signed in may create a tag; only the owner may update one.
     */
    public function authorize(): bool
    {
        $tag = $this->route('tag');

        return ! $tag instanceof Tag || $this->user()->can('update', $tag);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tags', 'title')
                    ->where('user_id', $this->user()->id)
                    ->ignore($this->route('tag')),
            ],
        ];
    }
}
