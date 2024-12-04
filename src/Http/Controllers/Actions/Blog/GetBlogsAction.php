<?php

namespace eightworx\Blogs\Http\Controllers\Actions\Blog;


use App\Http\Helpers\SearchByDate;
use eightworx\Blogs\Models\Blog;

class GetBlogsAction
{
    public function execute($request)
    {
        // Search Blogs
        $blogs = (new Blog)->newQuery()->with(['category','media']);

        // Search With Key
        if ($request->key) {
            $blogs->where(function ($q) use ($request) {
                return $q->when($request->key, function ($query) use ($request) {
                    return $query->where('title_en', 'LIKE', '%' . $request->key . '%')
                        ->orWhere('title_ar', 'LIKE', '%' . $request->key . '%')
                        ->orWhere('subtitle_en', 'LIKE', '%' . $request->key . '%')
                        ->orWhere('subtitle_ar', 'LIKE', '%' . $request->key . '%')
                        ->orWhere('description_en', 'LIKE', '%' . $request->key . '%')
                        ->orWhere('description_ar', 'LIKE', '%' . $request->key . '%')
                        ->orWhere('short_description_en', 'LIKE', '%' . $request->key . '%')
                        ->orWhere('short_description_ar', 'LIKE', '%' . $request->key . '%')
                        ->orWhere('slug', 'LIKE', '%' . $request->key . '%');
                });
            });
        }

        if ($request->input('is_published') === 'true') {
            $blogs = $blogs->where('is_published', true);
        }

        if ($request->has('sort_field')) {
            $sortField = $request->sort_field;
            $sortOrder = strtolower($request->input('sort_order'));
            if (!in_array($sortOrder, ['asc', 'desc'])) {
                $sortOrder = 'asc';
            }
            $blogs->orderBy($sortField, $sortOrder);
        }

        // Search With date ( created_at , updated_at )
        $search = new SearchByDate();
        $search->search($request, $blogs);

        // Return the result of the search query
        return $blogs;
    }
}
