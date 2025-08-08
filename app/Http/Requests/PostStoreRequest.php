<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only authenticated users can create posts
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'title'       => 'required|string|max:255',
            'body'        => 'required|string',
            //'is_published'=> 'boolean'
        ];
    }
}
