<?php

namespace eightworx\Blogs\Http\Controllers\Actions\Blog;

use Exception;
use eightworx\Blogs\Models\Blog;
use eightworx\Blogs\Transformers\BlogsResource;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Traits\ImageTrait;

class UpdateBlogAction
{
    use  ImageTrait;

    public function execute($data)
    {
        // Get the Blog by ID
        $blog = Blog::find($data['id']);

        // Check if blog is found
        if (!$blog) {
            return null;
        }

        // Initialize optimizer chain
        $optimizerChain = OptimizerChainFactory::create();
        
        // Handle gallery images
        $this->updateMultiMedia($blog, $data['featured_images'] ?? [], 'Blogs.featured_images', $optimizerChain);

        // Handle media files if provided
        if (isset($data['media_data']) && $data['media_type']) {
            if($data['media_type'] === 'video'){
                $this->updateMedia($blog, $data['media_data'], 'Blogs.media_data', $optimizerChain);
            }
             else{
                $blog->media_data = $data['media_data'];
                $blog->media_type = $data['media_type'];
                $blog->save();
            }
        }

        // Update blog data (exclude `featured_images` and `media_data` fields)
        $this->updateMedia($blog, $data['og_image'] ?? null, 'Blogs.og_image', $optimizerChain);
        $this->updateMedia($blog, $data['image'] ?? null, 'Blogs.image', $optimizerChain);

        // $updateData = collect($data)->except(['featured_images', 'media_data', 'media_type'])->toArray();
        $blog->update($data);

        $blog = $blog->refresh();

        // Return the Blog resource
        return new BlogsResource($blog);
    }
}
