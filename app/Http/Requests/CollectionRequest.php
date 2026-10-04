<?php

namespace App\Http\Requests;

use App\Models\Collection;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CollectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Anyone signed in may create a collection; only the owner may update one.
     */
    public function authorize(): bool
    {
        $collection = $this->route('collection');

        return ! $collection instanceof Collection || $this->user()->can('update', $collection);
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
                Rule::unique('collections', 'title')
                    ->where('user_id', $this->user()->id)
                    ->ignore($this->route('collection')),
            ],
        ];
    }
}
