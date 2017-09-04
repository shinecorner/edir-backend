<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Location extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'street_name',
        'street_number',
        'street_additional',
        'zip_code',
        'city', // ort/stadt
        'district', // regierungsbezirk
        'county', // landkreis
        'state',  // bundesland
        'latitude',
        'longitude'
    ];

    /**
     * @return \Illuminate\Support\Collection
     */
    public static function getCachedStates()
    {
        if (Cache::has('states')) {
            return Cache::get('states');
        }

        $returnData = self::select(DB::RAW('DISTINCT(state)'))->whereNotNull('state')->groupBy('state')->get();

        Cache::forever('states', $returnData);

        return $returnData;
    }
}
