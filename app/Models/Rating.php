<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rating extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'title',
        'description',
        'rating',
        'approved',
        'is_visible',
        'ip_address'
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'company_id', 'directory_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function directory()
    {
        return $this->belongsTo(Directory::class);
    }

    /**
     * @return mixed
     */
    public function scopeApproved()
    {
        return $this->where('ratings.approved', true);
    }

    /**
     * @return mixed
     */
    public function scopeVisible()
    {
        return $this->where('ratings.is_visible', true);
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

}
