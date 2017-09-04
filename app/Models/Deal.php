<?php

namespace App\Models;

use App\Traits\ImageHelper;
use App\Traits\Seo;
use App\Traits\Slug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deal extends Model
{
    use ImageHelper, Seo, Slug, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'summary',
        'description',
        'conditions',
        'date_start',
        'date_end',
        'regular_price',
        'discount_type',
        'discount_value',
        'discount_coupon',
        'image',
        'video_url',
        'product_url',
        'active',
        'approved',
        'seo_meta_title',
        'seo_meta_description'
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'category_deal_id', 'company_id', 'location_id'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'date_start', 'date_end'
    ];

    /**
     * The attributes that casted.
     * date: ->startOfDay()
     *
     * @var array
     */
    protected $casts = [
        'date_start' => 'date',
        'date_end' => 'date'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(CategoryDeal::class, 'category_deal_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function keywords()
    {
        return $this->morphMany(Keyword::class, 'keywordable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function gallery_images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * @return mixed
     */
    public function isApproved()
    {
        return $this->approved;
    }

    /**
     * @return mixed
     */
    public function isActive()
    {
        return $this->approved;
    }

    /**
     * @return bool
     */
    public function hasCoupon()
    {
        if ($this->discount_coupon) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getTimeRangeAttribute()
    {
        $timerange = $this->date_start->format('d.m.y');
        if ($this->date_end) {
            $timerange .= ' - ' . $this->date_end->format('d.m.y');
        }

        return $timerange;
    }

    /**
     * @return string
     */
    public function getDiscountAttribute()
    {
        if ($this->discount_type == 'fixed') {
            return $this->discount_value . ' Euro';
        } elseif ($this->discount_type == 'percent') {
            return $this->discount_value . ' %';
        }
    }
}
