<?php

namespace App\Models;

use App\Traits\ImageHelper;
use App\Traits\Seo;
use App\Traits\Slug;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
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
        'date_start',
        'date_end',
        'time_start',
        'time_end',
        'discount_coupon',
        'image',
        'video_url',
        'active',
        'approved',
        'valid_until',
        'seo_meta_title',
        'seo_meta_description',
        'regular_price',
        'discount_type',
        'discount_value'
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
        'date_start', 'date_end', 'valid_until'
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
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(CategoryEvent::class, 'category_event_id');
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
        $timerange = $this->date_start->formatLocalized('%d %b %Y');
        if ($this->time_start) {
            $timerange .= ' ' . $this->time_start;
        }
        if ($this->date_end) {
            $timerange .= ' - ' . $this->date_end->formatLocalized('%d %b %Y');

            if ($this->time_end) {
                $timerange .= ' ' . $this->time_end;
            }
        }

        return $timerange;
    }

    /**
     * @return string
     */
    public function getTimeStartAttribute()
    {
        if ($this->getOriginal('time_start')) {
            return Carbon::createFromFormat('H:i:s', $this->getOriginal('time_start'))->format('H:i');
        }

        return null;
    }

    /**
     * @return string
     */
    public function getTimeEndAttribute()
    {
        if ($this->getOriginal('time_end')) {
            return Carbon::createFromFormat('H:i:s', $this->getOriginal('time_end'))->format('H:i');
        }

        return null;
    }

    /**
     * @return string
     */
    public function getAddressLineAttribute()
    {
		$address = $this->location->street_name .' '. $this->location->street_number;
		$address .= $this->location->street_additional ? ' '.$this->location->street_additional.', ' : ', ';
		$address .= $this->location->zip_code . ' ' . $this->location->city . ', ';
		$address .= $this->location->state;

		return $address;
    }
}
