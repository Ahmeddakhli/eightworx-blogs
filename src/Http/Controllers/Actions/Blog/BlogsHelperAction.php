<?php

namespace eightworx\Blogs\Http\Controllers\Actions\Blog;

use eightworx\Blogs\Models\Blog;
use Modules\Lookups\Http\Controllers\Actions\Lookup\LookupsHelperByParentSlugAction;

class BlogsHelperAction
{
    public function execute($request)
    {
       // Instantiate the action class
       $lookupHelper = new LookupsHelperByParentSlugAction();

       // Call the method to fetch lookups
       $data = $lookupHelper->execute(['blog-categories']);

       return $data;
    }
}