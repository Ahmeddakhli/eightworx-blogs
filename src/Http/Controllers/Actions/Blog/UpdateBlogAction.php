<?php

namespace eightworx\Blogs\Http\Controllers\Actions\Blog;

use Exception;
use eightworx\Blogs\Models\Blog;
use eightworx\Blogs\Transformers\BlogsResource;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UpdateBlogAction
{
    public function execute($data)
    {
        // Get the Blog by ID
        $blog = Blog::find($data['id']);

        // Check if blog is found
        if (!$blog) {
            return null;
        }

        // // Handle featured images if provided
        // if (isset($data['featured_images']) && is_array($data['featured_images'])) {
        //     // Clear existing featured images
        //     $blog->clearMediaCollection('Blogs.featured_images');

        //     // Optimize and add new images
        //     $optimizerChain = OptimizerChainFactory::create();
        //     foreach ($data['featured_images'] as $image) {
        //         $mediaItem = $blog->addMedia($image)->toMediaCollection('Blogs.featured_images');
        //         $optimizerChain->optimize($mediaItem->getPath());
        //     }
        // }

        // Initialize optimizer chain
        $optimizerChain = OptimizerChainFactory::create();

        // Handle gallery images
        $this->handleFeaturedImages($blog, $data['featured_images'] ?? [], 'Blogs.featured_images', $optimizerChain);

        // Handle media files if provided
        if (isset($data['media_data']) && $data['media_type']) {
            $this->storeMedia($blog, $data['media_data'], $data['media_type']);
        }
        // Update blog data (exclude `featured_images` and `media_data` fields)
        $this->updateMedia($blog, $data['og_image'] ?? null, 'Blogs.og_image', $optimizerChain);
        $updateData = collect($data)->except(['featured_images', 'media_data', 'media_type'])->toArray();
        $blog->update($updateData);

        $blog = $blog->refresh();

        // Return the Blog resource
        return new BlogsResource($blog);
    }
    protected function updateMedia($project, $input, $collection)
    {
        if (is_null($input)) {
            $project->clearMediaCollection($collection);
            return;
        }

        if (is_string($input) && filter_var($input, FILTER_VALIDATE_URL)) {
            preg_match('/\/(\d+)\/.*$/', $input, $matches);
            $mediaId = $matches[1] ?? null;

            $currentMedia = $project->getFirstMedia($collection);
            if ($currentMedia && $currentMedia->id === $mediaId) {
                return;
            }

            $mediaToKeep = $project->getMedia($collection)->filter(function ($mediaItem) use ($mediaId) {
                return $mediaItem->id == $mediaId;
            });

            $project->clearMediaCollectionExcept($collection, $mediaToKeep);
        }

        if ($input instanceof UploadedFile) {
            $mediaItem = $project->addMedia($input)->toMediaCollection($collection);
            $this->optimizeMedia($mediaItem);
        }
    }
    protected function optimizeMedia($mediaItem)
    {
        $optimizerChain = OptimizerChainFactory::create();
        $optimizerChain->optimize($mediaItem->getPath());
    }
    protected function storeMedia(Blog $blog, $media, $mediaType)
    {
        switch ($mediaType) {
            case 'video':
                if ($media instanceof \Illuminate\Http\UploadedFile) {
                    $blog->clearMediaCollection('Blogs.media_data');
                    $blog->addMedia($media)->toMediaCollection('Blogs.media_data');
                } else {
                    throw new Exception('Expected an uploaded file for video.');
                }
                break;

            case 'url':
            case 'iframe':
                if (!is_string($media)) {
                    throw new Exception('Expected a string for URL or iframe.');
                }
                $blog->media_data = $media;
                $blog->save();
                break;

            default:
                throw new Exception('Unsupported media type: ' . $mediaType);
        }
    }

    private function handleFeaturedImages($blog, $images, $collection, $optimizerChain)
    {
        // if (empty($images)) {
        //     $blog->clearMediaCollection($collection);
        //     return;
        // }

        // $idsToKeep = [];
        // foreach ($images as $item) {
        //     if (empty($images)) {
        //         $blog->clearMediaCollection($collection);
        //         return;
        //     }

        //     if (is_string($item)) {
        //         // If the item is a URL or existing media identifier
        //         preg_match('/\/(\d+)\/.*$/', $item, $matches);
        //         $mediaId = $matches[1] ?? null;
        //         if ($mediaId) {
        //             $idsToKeep[] = $mediaId;
        //         } else {
        //             // Add new media from URL
        //             $mediaItem = $blog->addMediaFromUrl($item)->toMediaCollection($collection);
        //             $optimizerChain->optimize($mediaItem->getPath());
        //         }
        //     } elseif ($item instanceof \Illuminate\Http\UploadedFile) {
        //         // Add new uploaded file
        //         $mediaItem = $blog->addMedia($item)->toMediaCollection($collection);
        //         $optimizerChain->optimize($mediaItem->getPath());
        //     } else {
        //         throw new Exception('Unsupported media type in featured images.');
        //     }
        // }

        // // Remove media not in the list of IDs to keep
        // $blog->media()->where('collection_name', $collection)->whereNotIn('id', $idsToKeep)->delete();

        if (empty($images)) {
            $blog->clearMediaCollection($collection);
            return;
        }

        $idsToKeep = [];
        foreach ($images as $item) {
            if (is_string($item)) {
                preg_match('/\/(\d+)\/.*$/', $item, $matches);
                $mediaId = $matches[1] ?? null;
                if ($mediaId) {
                    $idsToKeep[] = $mediaId;
                }
            }
        }

        foreach ($blog->getMedia($collection) as $mediaItem) {
            if (!in_array($mediaItem->id, $idsToKeep)) {
                $mediaItem->delete();
            }
        }

        foreach ($images as $item) {
            if ($item instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                $mediaItem = $blog->addMedia($item)->toMediaCollection($collection);
                $optimizerChain->optimize($mediaItem->getPath());
            }
        }
    }
}
