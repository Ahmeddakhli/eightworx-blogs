<?php

namespace eightworx\Blogs\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use eightworx\Blogs\Transformers\BlogsResource;
use eightworx\Blogs\Http\Requests\StoreBlogsRequest;
use eightworx\Blogs\Http\Requests\DeleteBlogsRequest;
use eightworx\Blogs\Http\Requests\UpdateBlogsRequest;
use eightworx\Blogs\Http\Controllers\Actions\Blog\GetBlogsAction;
use eightworx\Blogs\Http\Controllers\Actions\Blog\StoreBlogAction;
use eightworx\Blogs\Http\Controllers\Actions\Blog\DeleteBlogAction;
use eightworx\Blogs\Http\Controllers\Actions\Blog\UpdateBlogAction;
use eightworx\Blogs\Http\Controllers\Actions\Blog\GetBlogByIdAction;
use eightworx\Blogs\Http\Controllers\Actions\Blog\AutoCompleteAction;

class BlogsController extends Controller
{
    // Define actions as private properties
    private GetBlogsAction $getBlogsAction;
    private GetBlogByIdAction $getBlogByIdAction;
    private StoreBlogAction $storeBlogAction;
    private UpdateBlogAction $updateBlogAction;
    private DeleteBlogAction $deleteBlogAction;
    private AutoCompleteAction $autoCompleteAction;

    /**
     * Create a new controller instance.
     *
     * @param GetBlogsAction $getBlogsAction
     * @param GetBlogByIdAction $getBlogByIdAction
     * @param StoreBlogAction $storeBlogAction
     * @param UpdateBlogAction $updateBlogAction
     * @param DeleteBlogAction $deleteBlogAction
     * @param AutoCompleteAction $autoCompleteAction
     */
    public function __construct(
        GetBlogsAction  $getBlogsAction,
        GetBlogByIdAction $getBlogByIdAction,
        StoreBlogAction   $storeBlogAction,
        UpdateBlogAction  $updateBlogAction,
        DeleteBlogAction  $deleteBlogAction,
        AutoCompleteAction $autoCompleteAction
    ) {
        $this->getBlogsAction = $getBlogsAction;
        $this->getBlogByIdAction = $getBlogByIdAction;
        $this->storeBlogAction = $storeBlogAction;
        $this->updateBlogAction = $updateBlogAction;
        $this->deleteBlogAction = $deleteBlogAction;
        $this->autoCompleteAction = $autoCompleteAction;
    }

    public function index(Request $request)
    {
        // Execute the search action to retrieve blogs based on the provided request.
        $blogs = $this->getBlogsAction->execute($request);
        // use this to return trans object
        $data = DataTables::of($blogs)
            ->addColumn('record', function ($blogs) {
                return (new BlogsResource($blogs));
            })->make(true)->original;

        // Return the response
        return $this->successResponse(null, $data);
    }

    public function autocomplete(Request $request)
    {
        $users = $this->autoCompleteAction->execute($request);

        // Return the response
        return $this->successResponse(null, $users);
    }

    public function indexFront(Request $request)
    {
        // Add 'is_front' to the request with a value of true or false
        $request->merge(['is_published' => true]);

        // Get the list of blogs
        $blogs = $this->getBlogsAction->execute($request);

        // Return the response
        return $this->successResponse(null, $blogs);
    }

    public function store(StoreBlogsRequest $request)
    {
        // Create a new blog
        $blog = $this->storeBlogAction->execute($request->all());

        // Return the response
        return $this->successResponse(__('blogs.created_successfully'), $blog);
    }

    public function update(UpdateBlogsRequest $request)
    {
        // Update the blog
        $blog = $this->updateBlogAction->execute($request->all());

        // Not Found
        if (!$blog) {
            return $this->errorResponse(__('blogs.not_found'), null, 404);
        }

        // Return the response
        return $this->successResponse(__('blogs.updated_successfully'), $blog);
    }

    public function destroy(DeleteBlogsRequest $request)
    {
        // Delete the blog
        $blog = $this->deleteBlogAction->execute($request->input('id'));

        // Not Found
        if (!$blog) {
            return $this->errorResponse(__('blogs.not_found'), null, 404);
        }

        // Return the response
        return $this->successResponse(__('blogs.deleted_successfully'), null);
    }

    public function show($id)
    {
        // Get the blog by ID
        $blog = $this->getBlogByIdAction->execute($id);

        // Not Found
        if (!$blog) {
            return $this->errorResponse(__('blogs.not_found'), null, 404);
        }

        // Return the response
        return $this->successResponse(null, $blog);
    }
}
