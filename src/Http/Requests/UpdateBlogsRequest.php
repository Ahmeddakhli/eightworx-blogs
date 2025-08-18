<?php

namespace eightworx\Blogs\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Http\Requests\BaseRequest;

class UpdateBlogsRequest extends BaseRequest
{
    protected function prepareForValidation()
    {
        if ($this->filled('slug')) {
            $this->merge(['slug' => Str::slug($this->input('slug'))]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'id' => 'required|exists:blogs,id,deleted_at,NULL',
            'post_type' => 'nullable|string|in:article,video,gallery',
            'title_en' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'sub_title_en' => 'nullable|string|max:255',
            'sub_title_ar' => 'nullable|string|max:255',
            'short_description_en' => 'nullable|string|max:500',
            'short_description_ar' => 'nullable|string|max:500',
            'description_en' => 'required|string',
            'description_ar' => 'nullable|string',
            'order' => 'nullable|integer|min:1',
            'media_type' => 'nullable|string|in:url,video,iframe',
            // 'media_data' => 'nullable|string',
            'views' => 'nullable|integer|min:0',
            'is_featured' => 'nullable|in:0,1',
            'is_published' => 'nullable|in:0,1',
            'published_at' => 'nullable|date',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('blogs')->ignore($this->id),
            ],
            'meta_title_en' => 'nullable|string|max:255',
            'meta_title_ar' => 'nullable|string|max:255',
            'meta_description_en' => 'nullable|string|max:500',
            'meta_description_ar' => 'nullable|string|max:500',
            'is_index' => 'nullable|in:0,1',
            'category_id' => 'required|exists:lookups,id',
            'featured_images' => 'nullable|array',
            'featured_images.*' => $this->imageOrUrlRule('featured_images'),
            'image' => $this->imageOrUrlRule('featured_images'),
        ];

        //  Conditional rules for media_data based on post_type and media_type
        if ($this->post_type === 'video') {
            if ($this->media_type === 'video') {
                $rules['media_data'] = $this->videoOrUrlRule('media_data');
            } elseif ($this->media_type === 'url') {
                $rules['media_data'] = 'required|url';
            } elseif ($this->media_type === 'iframe') {
                $rules['media_data'] = 'required|string';
            }
        } elseif ($this->post_type === 'article') {
            $rules['media_data'] = 'required|string';
        }

        return $rules;
    }

    /**
     * Add conditional validation for media_data and other fields based on post_type.
     */
    public function withValidator($validator)
    {
        $validator->sometimes('featured_images', 'required|array', function ($input) {
            return $input->post_type === 'gallery';
        });
    }

    protected function imageOrUrlRule($attribute)
    {
        return function ($attribute, $value, $fail) {
            if (is_null($value)) {
                return;
            }
            if (is_string($value)) {
                if (!filter_var($value, FILTER_VALIDATE_URL) || !preg_match('/\.(jpeg|jpg|png|gif|svg)$/', $value)) {
                    $fail('The ' . $attribute . ' must be a valid URL to an image.');
                }
                return;
            }

            if (is_object($value) && $value->isValid()) {
                if (!in_array($value->getClientOriginalExtension(), ['jpeg', 'jpg', 'png', 'gif', 'svg'])) {
                    $fail('The ' . $attribute . ' must be a valid image file.');
                }

                if ($value->getSize() > 2048 * 1024) {
                    $fail('The ' . $attribute . ' must not be greater than 2MB.');
                }

                return;
            }

            $fail('The ' . $attribute . ' must be a valid image file or a valid URL to an image.');
        };
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}