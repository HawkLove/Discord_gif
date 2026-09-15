<?php

namespace App\Http\Requests;

use App\Rules\ValidGiphyUrl;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'tag_id' => [
                'required',
                'integer',
                Rule::exists('tags', 'id')->where(function ($query): void {
                    $query->where(function ($inner): void {
                        $inner->whereNull('user_id')
                            ->orWhere('user_id', $this->user()?->id);
                    });
                }),
            ],
            'visibility' => ['required', 'in:public,private'],
            'giphy_url' => ['required_without:file', 'nullable', 'url', 'max:2048', new ValidGiphyUrl],
            'file' => ['required_without:giphy_url', 'nullable', 'file', 'mimes:gif,jpg,jpeg,png,webp', 'max:8192'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tag_id.required' => 'Choose a tag.',
            'tag_id.exists' => 'Choose a tag.',
            'giphy_url.required_without' => 'Add a Giphy link or upload a file.',
            'file.required_without' => 'Add a Giphy link or upload a file.',
        ];
    }
}
