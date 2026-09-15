<?php

namespace App\Http\Requests;

use App\Models\Tag;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StorePersonalTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, list<string|ValidationRule>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
        ];
    }

    /**
     * @return list<callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->has('name')) {
                    return;
                }

                $slug = Tag::slugFrom($this->string('name')->toString());

                if ($slug === '') {
                    $validator->errors()->add('name', 'Use letters or numbers in the tag name.');

                    return;
                }

                $exists = Tag::query()
                    ->availableTo($this->user())
                    ->where('slug', $slug)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('name', 'That tag already exists.');
                }
            },
        ];
    }
}
