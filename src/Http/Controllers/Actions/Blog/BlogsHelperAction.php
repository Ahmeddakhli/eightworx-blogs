<?php

namespace eightworx\Blogs\Http\Controllers\Actions\Blog;

use eightworx\Blogs\Models\Blog;

class BlogsHelperAction
{
    public function execute($request)
    {
        // // Init Data
        // $data = null;

        // // Check if the request has 'form_name' parameter
        // if ($request->form_name) {
        //     switch ($request->form_name) {
        //         case 'store':
        //             $data = $this->store();
        //             break;
        //         case 'update':
        //             $data = $this->update($request);
        //             break;
        //         default:
        //             $data = null;
        //             break;
        //     }
        // }

        // // Return
        // return $data;

        $blogs = Blog::select('title_en as label', 'id as value')->get();

        // Return
        return $blogs;
    }

    // private function store()
    // {
    //     $blogs = Blog::select('title_en as label', 'id as value')->get();

    //     // Return
    //     return [
    //         $blogs
    //     ];
    // }

    // private function update($request)
    // {
    //     $blogs = Blog::select('title_en as label', 'id as value')->where('id', '!=', $request->input('id'))->get();

    //     // Return
    //     return [
    //         $blogs
    //     ];
    // }
}
