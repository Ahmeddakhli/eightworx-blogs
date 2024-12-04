<?php

namespace eightworx\Blogs\Http\Controllers\Actions\Blog;

use eightworx\Blogs\Models\Blog;
use eightworx\Blogs\Transformers\BlogsResource;

class GetBlogBySlugAction
{
    public function execute($slug)
    {
        // Get the Blog
        $blog = Blog::where('slug', $slug)->first();

        // Not Found!
        if (!$blog) {
            return null;
        }

        // Return Resource
        return new BlogsResource($blog);
    }
}
