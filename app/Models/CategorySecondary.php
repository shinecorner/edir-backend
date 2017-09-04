<?php

namespace App\Models;

use App\Traits\ImageHelper;
use App\Traits\Seo;
use App\Traits\Slug;
use Illuminate\Database\Eloquent\Model;

class CategorySecondary extends Model
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
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'category_primary_id'
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'parent'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_categories', 'category_secondary_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(CategoryPrimary::class, 'category_primary_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function keywords()
    {
        return $this->morphMany(Keyword::class, 'keywordable');
    }
}
