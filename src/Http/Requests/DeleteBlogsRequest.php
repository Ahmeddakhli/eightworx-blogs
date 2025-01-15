<?php

namespace eightworx\Blogs\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteBlogsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'id' => 'required|exists:blogs,id,deleted_at,NULL',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
