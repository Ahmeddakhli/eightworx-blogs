<?php

namespace eightworx\Blogs\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreBlogsRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        // Normalize the slug if it is provided
        if ($this->filled('slug')) {
            $this->merge([
                'slug' => Str::slug($this->input('slug')),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'post_type' => 'nullable|string|in:article,video,gallery',
            'title_en' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'sub_title_en' => 'nullable|string|max:255',
            'sub_title_ar' => 'nullable|string|max:255',
            'short_description_en' => 'nullable|string|max:500',
            'short_description_ar' => 'nullable|string|max:500',
            'description_en' => 'required|string',
            'description_ar' => 'nullable|string',
            'order' => 'required|integer|min:1',
            'media_type' => 'nullable|string|in:url,video,iframe',
            'media_data' => 'nullable|string',
            'views' => 'nullable|integer|min:0',
            'is_featured' => 'nullable|in:0,1',
            'is_published' => 'nullable|in:0,1',
            'published_at' => 'nullable|date',
            'slug' => 'nullable|string|max:255|unique:blogs,slug,NULL,id,deleted_at,NULL',
            'meta_title_en' => 'nullable|string|max:255',
            'meta_title_ar' => 'nullable|string|max:255',
            'meta_description_en' => 'nullable|string|max:500',
            'meta_description_ar' => 'nullable|string|max:500',
            'is_index' => 'nullable|in:0,1',
            'category_id' => 'required|exists:lookups,id',
            'featured_images' => 'nullable|array',
            'featured_images.*' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ];

        // Additional rules for video and article types
        if ($this->post_type === 'video') {
            // For video, media_data must be a file (video)
            $rules['media_data'] = 'required|file|mimetypes:video/mp4,video/x-m4v,video/*';
        } elseif ($this->post_type === 'article') {
            // For article, media_data must be a string (URL or iframe)
            $rules['media_data'] = 'required|string';
        }

        return $rules;
    }

    /**
     * Add conditional validation for `media_data` and `media_type` fields based on post_type.
     */
    public function withValidator($validator)
    {
        // For 'video' post type, 'media_data' must be a file
        $validator->sometimes('media_data', 'required|file|mimetypes:video/mp4,video/x-m4v,video/*', function ($input) {
            return $input->post_type === 'video';
        });

        // For 'article' post type and 'media_type' of 'url', 'media_data' should be a URL string
        $validator->sometimes('media_data', 'required|string|url', function ($input) {
            return $input->post_type === 'article' && $input->media_type === 'url';
        });

        // For 'article' post type and 'media_type' of 'iframe', 'media_data' should be a string
        $validator->sometimes('media_data', 'required|string', function ($input) {
            return $input->post_type === 'article' && $input->media_type === 'iframe';
        });

        // For 'gallery' post type, 'featured_images' must be an array
        $validator->sometimes('featured_images', 'required|array', function ($input) {
            return $input->post_type === 'gallery';
        });
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
