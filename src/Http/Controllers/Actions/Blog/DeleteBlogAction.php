<?php

namespace eightworx\Blogs\Http\Controllers\Actions\Blog;

use eightworx\Blogs\Models\Blog;

class DeleteBlogAction
{
    public function execute($id)
    {
       // Delete the Blog
       $blog = Blog::where('id', $id)->first();

       // Not Found!
       if (!isset($blog)) {
           return null;
       }

       // Clear the media collection for the slider
       if ($blog->hasMedia('Blogs.featured_images')) {
           $blog->clearMediaCollection('Blogs.featured_images');
       }

       // Delete
       $blog->delete();

       return true;
    }
}
