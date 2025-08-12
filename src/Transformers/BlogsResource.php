<?php

namespace eightworx\Blogs\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'post_type' => $this->post_type,
            'title_en' => $this->title_en,
            'title_ar' => $this->title_ar,
            'default_title' => $this->default_title,
            'image' => $this->getFirstMediaUrl('Blogs.image'),
            'sub_title_en' => $this->sub_title_en,
            'sub_title_ar' => $this->sub_title_ar,
            'short_description_en' => $this->short_description_en,
            'short_description_ar' => $this->short_description_ar,
            'description_en' => $this->description_en,
            'description_ar' => $this->description_ar,
            'default_description' => $this->default_description,
            'slug' => $this->slug,
            'order' => $this->order,
            'views' => $this->views,
            'is_featured' => $this->is_featured ? 1 : 0,
            'is_published' => $this->is_published ? 1 : 0,
            'published_at' => $this->published_at ? $this->published_at->timezone('Africa/Cairo')->toDateTimeString() : null,
            'meta_title_en' => $this->meta_title_en,
            'meta_title_ar' => $this->meta_title_ar,
            'meta_description_en' => $this->meta_description_en,
            'meta_description_ar' => $this->meta_description_ar,
            'meta_keywords_en' => $this->meta_keywords_en,
            'meta_keywords_ar' => $this->meta_keywords_ar,
            'og_title_ar' => $this->og_title_ar,
            'og_title_en' => $this->og_title_en,
            'og_description_ar' => $this->og_description_ar,
            'og_description_en' => $this->og_description_en,
            'og_image' => $this->getFirstMediaUrl('Blogs.og_image'),
            'is_index' => $this->is_index,
            'category_id' => optional($this->category)->id,
            'featured_images' => $this->getMedia('Blogs.featured_images')->map(function ($media) {
                return $media->getUrl();
            }),
            'thumb' => $this->getFirstMediaUrl('Blogs.featured_images', 'thumb'),
            'featured_images_432_555' => $this->getFirstMediaUrl('Blogs.featured_images', 'featured_images_432_555'),
            'featured_images_303_303' => $this->getFirstMediaUrl('Blogs.featured_images', 'featured_images_303_303'),
            'featured_images_364_849' => $this->getFirstMediaUrl('Blogs.featured_images', 'featured_images_364_849'),
            'media_type' => $this->media_type,
            'media_data' => $this->media_data,
            'created_at' => $this->created_at ? $this->created_at->timezone('Africa/Cairo')->toDateTimeString() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->timezone('Africa/Cairo')->toDateTimeString() : null,
            'created_since' => $this->created_at ? $this->created_at->timezone('Africa/Cairo')->diffForHumans() : null,
            'updated_since' => $this->updated_at ? $this->updated_at->timezone('Africa/Cairo')->diffForHumans() : null,
        ];
    }
}
