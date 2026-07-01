<?php

namespace eightworx\Blogs\Http\Controllers\Actions\Blog;

use eightworx\Blogs\Models\Blog;
use eightworx\Blogs\Transformers\BlogsResource;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Exception;

class StoreBlogAction
{
    public function execute($data)
    {
        // Create Blog
        $blog = Blog::create($data);

        // Sync related projects if provided
        if (isset($data['project_ids']) && is_array($data['project_ids'])) {
            $blog->projects()->sync($data['project_ids']);
        }

        // Store the image if provided
        if (isset($data['featured_images']) && is_array($data['featured_images'])) {
            $optimizerChain = OptimizerChainFactory::create(); // Reuse optimizer chain instance
            foreach ($data['featured_images'] as $featured_image) {
                $mediaItem = $blog->addMedia($featured_image)->toMediaCollection('Blogs.featured_images');

                // Optimize the uploaded media
                $optimizerChain->optimize($mediaItem->getPath());
            }
        }
        if (isset($data['og_image'])) {
            // Add the image to media collection
            $mediaItem = $blog->addMediaFromRequest('og_image')->toMediaCollection('Blogs.og_image');

            // Optimize the image
            $optimizerChain = OptimizerChainFactory::create();
            $optimizerChain->optimize($mediaItem->getPath());
        }
        // Handle media files if provided
        if (isset($data['media_data']) && $data['media_type']) {
            $this->storeMedia($blog, $data['media_data'], $data['media_type']);
        }
        if (isset($data['image'])) {
            // Add the image to media collection
            $mediaItem = $blog->addMedia($data['image'])->toMediaCollection('Blogs.image');

            // Optimize the image
            $optimizerChain = OptimizerChainFactory::create();
            $optimizerChain->optimize($mediaItem->getPath());
        }

        // Refresh the blog to get updated data
        $blog->refresh();

        // Load related projects so they are included in the response
        $blog->load('projects');

        // Return Blog Resource
        return new BlogsResource($blog);
    }

    protected function storeMedia(Blog $blog, $media, $mediaType)
    {
        switch ($mediaType) {
            case 'video':
                if ($media instanceof \Illuminate\Http\UploadedFile) {
                    $blog->addMedia($media)->toMediaCollection('Blogs.media_data');
                } else {
                    throw new Exception('Expected an uploaded file for video.');
                }
                break;

            case 'url':
            case 'iframe':
                $blog->media_data = $media;
                $blog->save();
                break;

            default:
                throw new Exception('Unsupported media type: ' . $mediaType);
        }
    }
}
