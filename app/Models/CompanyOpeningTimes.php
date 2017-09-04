<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyOpeningTimes extends Model
{
    const WEEKDAYS = [
        'monday' => 'Montag',
        'tuesday' => 'Dienstag',
        'wednesday' => 'Mittwoch',
        'thursday' => 'Donnerstag',
        'friday' => 'Freitag',
        'saturday' => 'Samstag',
        'sunday' => 'Sonntag'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'weekday',
        'open_time',
        'close_time',
        'open_time_additional',
        'close_time_additional',
        'day_closed'
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'company_id'
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'company'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getOpenTimeAttribute($value)
    {
        return substr($value, 0, 5);
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getCloseTimeAttribute($value)
    {
        return substr($value, 0, 5);
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getOpenTimeAdditionalAttribute($value)
    {
        return substr($value, 0, 5);
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getCloseTimeAdditionalAttribute($value)
    {
        return substr($value, 0, 5);
    }

    /**
     * @return string
     */
    public function getWochentagAttribute()
    {
        return self::WEEKDAYS[$this->weekday];
    }
}
