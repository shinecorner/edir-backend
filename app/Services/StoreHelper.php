<?php namespace App\Services;

use App\Models\CategorySecondary;
use App\Models\CompanyOpeningTimes;
use App\Models\Keyword;
use App\Models\Location;

class StoreHelper
{
    /**
     * @param $request
     * @param $data
     */
    public function storeKeywords($request, $data)
    {
        $data->keywords()->delete();

        if ($request->get('keywords')) {
            $keywords = collect($request->get('keywords'))->map(function ($keyword) {
                return new Keyword([
                    'keyword' => $keyword,
                ]);
            });

            $data->keywords()->saveMany($keywords);
        }
    }

    /**
     * @param $category_secondary_ids
     * @param $data
     */
    public function storeCategories($category_secondary_ids, $data)
    {
        $data->categories()->detach();
        if (!empty($category_secondary_ids)) {

            $max_number_categories = $data->listing_level == 'premium' ? 5 : 1;

            collect($category_secondary_ids)->map(function ($cat_id) use ($data, $max_number_categories) {
                if ($data->categories->count() <= $max_number_categories) {
                    $cat_secondary = CategorySecondary::findOrFail($cat_id);
                    $data->categories()->save($cat_secondary, [
                        'company_id' => $data->id,
                        'category_primary_id' => $cat_secondary->parent->id
                    ]);
                }
            });

        }
    }

    /**
     * @param $request
     * @param $data
     */
    public function storeOpeningTimes($request, $data)
    {
        foreach (CompanyOpeningTimes::WEEKDAYS as $key => $weekday) {
            if (($request->$key['open_time'] && $request->$key['close_time']) or isset($request->$key['day_closed'])) {
                $opening_time = CompanyOpeningTimes::firstOrNew(['company_id' => $data->id, 'weekday' => $key]);
                $closed = isset($request->$key['day_closed']);
                $opening_time->fill([
                    'weekday' => $key,
                    'open_time' => ($closed ? null : $request->$key['open_time']),
                    'close_time' => ($closed ? null : $request->$key['close_time']),
                    'open_time_additional' => ($closed ? null : $request->$key['open_time_additional']),
                    'close_time_additional' => ($closed ? null : $request->$key['close_time_additional']),
                    'day_closed' => ($closed ? '1' : null),
                ]);
                $opening_time->company()->associate($data);
                $opening_time->save();
            } else {
                CompanyOpeningTimes::where('company_id', $data->id)->where('weekday', $key)->delete();
            }
        }
    }

    /**
     * @param $request
     * @param $data
     */
    public function storeLocation($request, $data)
    {
        if ($data->location) {
            $location = Location::find($data->location->id);
            $location->update($request->get('location'));

            return;
        }

        $location = Location::create($request->get('location'));
        $data->location()->associate($location);
    }

}