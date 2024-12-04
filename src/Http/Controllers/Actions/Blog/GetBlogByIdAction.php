<?php

namespace eightworx\Blogs\Http\Controllers\Actions\Blog;

use eightworx\Blogs\Models\Blog;
use eightworx\Blogs\Transformers\BlogsResource;

class GetBlogByIdAction
{
    public function execute($id)
    {
        // Get the Blog
        $blog = Blog::with(['category','media'])->find($id);

        // Not Found!
        if (!$blog) {
            return null;
        }

        // Return Resource
        return new BlogsResource($blog);
    }
}
