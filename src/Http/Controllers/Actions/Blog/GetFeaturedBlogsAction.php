<?php

namespace eightworx\Blogs\Http\Controllers\Actions\Blog;

use eightworx\Blogs\Models\Blog;
use eightworx\Blogs\Transformers\BlogsResource;

class GetFeaturedBlogsAction
{
    public function execute()
    {
        // Get featured blog
        $featuredBlog = Blog::where('is_featured', 1)->with(['category','media'])->limit(5)->get();
        
        // Not Found!
        if (!$featuredBlog) {
            return null;
        }

        // Return Resource
        return BlogsResource::collection($featuredBlog);
    }
}
