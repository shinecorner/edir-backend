<?php

namespace App\Models;

use App\Traits\ImageHelper;
use App\Traits\Seo;
use App\Traits\Slug;
use Illuminate\Database\Eloquent\Model;

class CategoryDeal extends Model
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
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function companies()
    {
        return $this->hasManyThrough(
            Company::class, Deal::class,
            'category_deal_id', 'id', 'id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function keywords()
    {
        return $this->morphMany(Keyword::class, 'keywordable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deals()
    {
        return $this->hasMany(Deal::class);
    }
}
