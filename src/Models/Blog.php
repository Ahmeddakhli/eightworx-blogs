<?php

namespace eightworx\Blogs\Models;

use App\Traits\ApiResourceTrait;
use Spatie\MediaLibrary\HasMedia;
use ApiPlatform\Metadata\ApiResource;

use Modules\Lookups\Models\Lookup;
use Wildside\Userstamps\Userstamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Traits\Sluggable;
// #[ApiResource]
class Blog extends Model implements HasMedia
{
    use  ApiResourceTrait, HasFactory, SoftDeletes, Userstamps, InteractsWithMedia, Sluggable;
    use QueryCacheable;
    
    /**
     * Get the class being used to provide a User.
     *
     * @return string
     */
    protected function getUserClass()
    {
        return "Modules\Users\Models\User";
    }
 
    public $cacheFor = 3600;

    public $cachePrefix = 'blog_';
    public $cacheTags = ['blog'];    
    protected static $flushCacheOnUpdate = true;

    protected function getCacheTags(): array
    {
        return ['media'];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title_en',
        'title_ar',
        'sub_title_en',
        'sub_title_ar',
        'short_description_en',
        'short_description_ar',
        'description_en',
        'description_ar',
        'post_type',
        'order',
        'media_type',
        'media_data',
        'views',
        'is_featured',
        'is_published',
        'published_at',
        'slug',
        'meta_title_en',
        'meta_title_ar',
        'meta_description_en',
        'meta_description_ar',
        'is_index',
        'category_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $dates = ['deleted_at'];

    protected static $recordEvents = ['created', 'updated', 'deleted'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s',
        'published_at' => 'datetime:Y-m-d H:m:s',
        'is_index' => 'integer'
    ];

    protected $appends = ['default_title', 'default_description'];

    public function getDefaultTitleAttribute()
    {
        // Get the current locale
        $locale = app()->getLocale();

        // Return the title
        if ($locale == 'ar' && !empty($this->title_ar)) {
            return $this->title_ar;
        } else {
            return $this->title_en ?? $this->title_ar;
        }
    }

    public function getDefaultDescriptionAttribute()
    {
        // Get the current locale
        $locale = app()->getLocale();

        // Return the description
        if ($locale == 'ar' && !empty($this->description_ar)) {
            return $this->description_ar;
        } else {
            return $this->description_en ?? $this->description_ar;
        }
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', 1);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', 1);
    }
  
    // Register media collections
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('Blogs.featured_images')
            ->useDisk('public')
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion('thumb')
                    ->width(100)
                    ->height(100)
                    ->nonQueued();

                $this->addMediaConversion('featured_images_432_555')
                    ->width(432)
                    ->height(555)
                    ->nonQueued();

                $this->addMediaConversion('featured_images_303_303')
                ->width(303)
                ->height(303)
                ->nonQueued();

                $this->addMediaConversion('featured_images_364_849')
                ->width(364)
                ->height(849)
                ->nonQueued();
            });

        $this->addMediaCollection('Blogs,media_data')
            ->acceptsFile(function ($file) {
                return in_array($file->getMimeType(), ['video/mp4', 'video/mov', 'video/avi']);
            });
    }

    // Blog Category
    public function category()
    {
        return $this->belongsTo(Lookup::class, 'category_id');
    }
       /**
     * Define the API resource
     */
    public static function apiResource()
    {
        return self::apiResourceForModule('Blog');
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('ordered', function (Builder $builder) {
            $builder->orderBy('order', 'DESC');
        });
    }

}

Blog::apiResource();
