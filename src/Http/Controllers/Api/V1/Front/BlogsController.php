<?php

namespace eightworx\Blogs\Http\Controllers\Api\V1\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use eightworx\Blogs\Http\Controllers\Actions\Blog\GetBlogsAction;
use eightworx\Blogs\Transformers\BlogsResource;
use eightworx\Blogs\Http\Controllers\Actions\Blog\GetBlogBySlugAction;
use eightworx\Blogs\Http\Controllers\Actions\Blog\GetBlogByIdAction;
use eightworx\Blogs\Transformers\BlogCardResource;
use Yajra\DataTables\Facades\DataTables;

class BlogsController extends Controller
{
    // Define actions as private properties
    private GetBlogsAction $getBlogsAction;
    private GetBlogBySlugAction $getBlogBySlugAction;

    private GetBlogByIdAction $getBlogByIdAction;

    /**
     * Create a new controller instance.
     *
     * @param GetBlogsAction $getBlogsAction
     */
    public function __construct(
        GetBlogsAction  $getBlogsAction,
        GetBlogBySlugAction $getBlogBySlugAction,
        GetBlogByIdAction $getBlogByIdAction,
    ) {
        $this->getBlogsAction = $getBlogsAction;
        $this->getBlogBySlugAction = $getBlogBySlugAction;
        $this->getBlogByIdAction = $getBlogByIdAction;
    }

    public function index(Request $request)
    {
        // Execute the action to retrieve blogs based on the provided request.
        $blogs = $this->getBlogsAction->execute($request);
        $blogs = BlogCardResource::collection($blogs->latest()->published()->featured()->limit($request->length ?? 10)->get());

        // Return the response
        return $this->successResponse(null, $blogs);
    }

    public function paginatedBlogs(Request $request)
    {
        // Execute the search action to retrieve blogs based on the provided request.
        $blogs = $this->getBlogsAction->execute($request);

        // use this to return trans object
        $data = DataTables::of($blogs)
            ->addColumn('record', function ($blogs) {
                return (new BlogCardResource($blogs))->toArray(request());
            })->make(true)->original;

        // Return the response
        return $this->successResponse(null, $data);
    }

    public function show(Request $request)
    {
        // Get the blog by Slug
        $blog = $this->getBlogByIdAction->execute($request->id);

        // Not Found
        if (!$blog) {
            return $this->errorResponse(__('blogs.not_found'), null, 404);
        }

        // Return the response
        return $this->successResponse(null, $blog);
    }

    // public function showBySlug($slug)
    // {
    //     // Get the blog by Slug
    //     $blog = $this->getBlogBySlugAction->execute($slug);

    //      // Not Found
    //      if (!$blog) {
    //         return $this->errorResponse(__('blogs.not_found'), null, 404);
    //     }

    //     // Return the response
    //     return $this->successResponse(null, $blog);
    // }
}
