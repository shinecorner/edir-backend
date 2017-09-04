<?php

namespace App\Models;

use App\Traits\ImageHelper;
use App\Traits\Seo;
use App\Traits\Slug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CategoryPrimary extends Model
{
    use ImageHelper, Seo, Slug;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'count',
        'image',
        'description',
        'seo_meta_title',
        'seo_meta_description'
    ];

    /**
     * @return \Illuminate\Support\Collection
     */
    public static function getCachedCategories()
    {
        if (Cache::has('categories')) {
            return Cache::get('categories');
        }

        //closure could be removed, but views would have to updated.
        $returnData = CategoryPrimary::all()->map(function ($category) {
            return (object)[
                'category' => $category,
            ];
        });

        Cache::forever('categories', $returnData);

        return $returnData;
    }

    /**
     * @param bool $refresh
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getCachedSubCategories($refresh = false)
    {
        if (Cache::has('subcategories') and !$refresh) {
            return Cache::get('subcategories');
        }

        $returnData = CategoryPrimary::all()->map(function ($category) {
            return (object)[
                'category' => $category,
                'subcategories' => CategorySecondary::where('category_primary_id', $category->id)
                    ->orderBy('count', 'desc')
                    ->get()
            ];
        });

        Cache::forever('subcategories', $returnData);

        return $returnData;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_categories', 'category_primary_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subcategories()
    {
        return $this->hasMany(CategorySecondary::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function keywords()
    {
        return $this->morphMany(Keyword::class, 'keywordable');
    }
}
