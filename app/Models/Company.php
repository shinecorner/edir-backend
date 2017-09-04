<?php

namespace App\Models;

use App\Traits\ImageHelper;
use App\Traits\Seo;
use App\Traits\Slug;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Sleimanx2\Plastic\Searchable;

class Company extends Model implements AuditableContract
{
    use ImageHelper, Seo, Slug, Searchable, Auditable;

    /**
     * Attributes to exclude from the Audit. Timestamps are excluded by default (see setting auditTimestamps)
     *
     * @var array
     */
    protected $auditExclude = [
        'seo_meta_title',
        'seo_meta_description',
        'slug'
    ];

    /**
     * Should the timestamps be audited?
     *
     * @var bool
     */
    protected $auditTimestamps = false; //false is default value

    /**
     * Audit threshold. Only the latest x audit records are saved. Default: all records are saved
     *
     * @var int
     */
//	protected $auditThreshold = 100; //bug? seems like the first x records are saved

    /**
     * Used by Slug trait: Generate unique slug from this model field
     *
     * @var string
     */
    protected $slug_from_key = 'name';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'mobile',
        'fax',
        'listing_status',
        'listing_level',
        'listing_valid_until',
        'www',
        'status',
        'summary',
        'description',
        'image',
        'video_url',
        'seo_meta_title',
        'seo_meta_description'
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'user_id', 'location_id'
    ];

    /**
     * Elasticsearch Document
     *
     * @return array
     */
    public function buildDocument()
    {
        $categoryPrimaries = collect(array_unique(array_merge(
            $this->categories->pluck('parent.name')->toArray()
        )))->toArray();

        $categorySecondaries = collect(array_unique(array_merge(
            $this->categories->pluck('name')->toArray()
        )))->toArray();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'zip_code' => $this->location->zip_code,
            'city' => $this->location->city,
            'county' => $this->location->county,
            'district' => $this->location->district,
            'state' => $this->location->state,
            'coordinates' => ['lat' => $this->location->latitude, 'lon' => $this->location->longitude],
            'listing_status' => $this->listing_status,
            'listing_level' => $this->listing_level,
            'category_primary' => $categoryPrimaries,
            'category_secondary' => $categorySecondaries,
            'keywords' => $this->keywords->pluck('keyword')
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany(CategorySecondary::class, 'company_categories');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function keywords()
    {
        return $this->morphMany(Keyword::class, 'keywordable');
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
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function gallery_images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files()
    {
        return $this->hasMany(CompanyFile::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deals()
    {
        return $this->hasMany(Deal::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function opening_times()
    {
        return $this->hasMany(CompanyOpeningTimes::class);
    }

    /**
     * @return mixed
     */
    public function scopePremium()
    {
        return $this->where('listing_level', 'premium');
    }

    /**
     * @return bool
     */
    public function hasPremium()
    {
        if ($this->listing_level == 'premium') {
            return true;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function scopeActive()
    {
        return $this->where('listing_status', true);
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

    /**
     * Date is saved as string but returned as carbon, so that audit does not show an updated field while saving
     *
     * @return Carbon
     */
    public function getListingValidUntilAttribute()
    {
        return Carbon::createFromFormat('Y-m-d', $this->getOriginal('listing_valid_until'));
    }

    /**
     * Return keywords as comma separated string
     *
     * @return string
     */
    public function getSeoKeywordsAttribute()
    {
        return implode(', ', $this->keywords->pluck('keyword')->toArray());
    }

    /**
     * Format audit data before saving to audit table
     *
     * @param array $data
     *
     * @return array
     */
    public function transformAudit(array $data)
    {
        //Format audit values
        if (array_has($data, 'new_values.listing_valid_until')) {
            array_set($data, 'new_values.listing_valid_until', Carbon::createFromFormat('Y-m-d', array_get($data, 'new_values.listing_valid_until'))->format('d.m.Y'));
        }
		if(array_has($data, 'old_values.listing_valid_until')) {
			array_set($data, 'old_values.listing_valid_until', Carbon::createFromFormat('Y-m-d', array_get($data, 'old_values.listing_valid_until'))->format('d.m.Y'));
		}
        if (array_has($data, 'new_values.listing_status')) {
            array_set($data, 'new_values.listing_status', $this->listing_status == 1 ? 'aktiv' : 'deaktiviert');
        }
		if(array_has($data, 'old_values.listing_status')) {
			array_set($data, 'old_values.listing_status', array_get($data, 'old_values.listing_status') == 1 ? 'aktiv' : 'deaktiviert');
		}
        if (array_has($data, 'new_values.location_id')) {
            array_set($data, 'new_values.location_id', $this->location_id . ' (' . $this->location->zip_code . ' ' . $this->location->city . ')');
        }
        if (array_has($data, 'new_values.user_id') and ! is_null(array_get($data, 'new_values.user_id'))) {
            array_set($data, 'new_values.user_id', $this->owner->id . ' (' . $this->owner->name . ')');
        }

        //Format audit keys
        $key_transformer = [
            'name' => 'Firmenname',
            'email' => 'Email',
            'phone' => 'Telefon',
            'mobile' => 'Mobiltelefon',
            'fax' => 'Fax',
            'www' => 'Internetadresse',
            'listing_level' => 'Level',
            'listing_status' => 'Status',
            'listing_valid_until' => 'Listing gültig bis',
            'summary' => 'Kurzbeschreibung',
            'description' => 'Beschreibung',
            'image' => 'Logo',
            'video_url' => 'Video',
            'location_id' => 'Besitzer',
        ];

        //rename keys
        foreach ($key_transformer as $key => $value) {
            if (array_has($data, 'new_values.' . $key)) {
                array_set($data, 'new_values.' . $value, array_get($data, 'new_values.' . $key));
                array_forget($data, 'new_values.' . $key);
            }

			if (array_has($data, 'old_values.' . $key)) {
				array_set($data, 'old_values.' . $value, array_get($data, 'old_values.' . $key));
				array_forget($data, 'old_values.' . $key);
			}
        }

        return $data;
    }

}