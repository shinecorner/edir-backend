<?php namespace App\Traits;


use Illuminate\Database\Eloquent\Model;

trait Slug
{
    /**
     * Modified from https://github.com/spatie/laravel-sluggable/blob/master/src/HasSlug.php
     *
     * assumes slug field = slug and title as clear text key name
     */

    /**
     * Boot the trait.
     *
     */
    protected static function bootSlug()
    {
        static::creating(function (Model $model) {
            $model->generateUniqueSlug();
        });
        static::saving(function (Model $model) {
            $model->generateUniqueSlug();
        });
        static::updating(function (Model $model) {
            $model->generateUniqueSlug();
        });
    }
//
//	/**
//	 * overwrite this in Model
//	 *
//	 * @return mixed
//	 */
//	abstract function generateSlug();

    /**
     * Make the given slug unique.
     */
    protected function generateUniqueSlug()
    {
        $slug_from_key = 'name';

        //update slug nur wenn title sich geändert hat
        if ($this->isDirty($slug_from_key) or empty($this->slug)) {
            $first_slug = str_slug($this->$slug_from_key);
            $slug = $first_slug;
            while ($this->otherRecordExistsWithSlug($slug) || $slug === '') {
                $slug = ($slug ? $first_slug . '-' : "") . rand(1E5, 1E7);
            }
            $this->slug = $slug;
        }
    }

    /**
     * Determine if a record exists with the given slug.
     */
    protected function otherRecordExistsWithSlug($slug)
    {
        return (bool)static::whereSlug($slug)->first();
    }

}