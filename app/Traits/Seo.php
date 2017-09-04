<?php namespace App\Traits;

trait Seo
{
    /**
     * Set the Slug automatically
     *
     * @param $value
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;

        if($value) {
            $this->attributes['seo_meta_title'] = $value;
        }
    }

    /**
     * @param $value
     */
    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = $value;

        if($value) {
            $this->attributes['seo_meta_description'] = str_limit(strip_tags($value), $limit = 60, $end = '...');
        }
    }

    /**
     * @return array
     */
    public function getSeoKeywordsAttribute()
    {
        if($this->keywords->count() > 0) {
            return implode(', ', $this->keywords->pluck('keyword')->toArray());
        }
    }
}
