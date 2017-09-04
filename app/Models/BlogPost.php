<?php

namespace App\Models;

use App\Traits\ImageHelper;
use App\Traits\Seo;
use App\Traits\Slug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogPost extends Model
{
    use Slug, Seo, SoftDeletes, ImageHelper;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'slug',
        'name',
        'description',
        'image',
        'seo_meta_title',
        'seo_meta_description'
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
     * Used by Slug trait: Generate unique slug from this model field
     *
     * @var string
     */
    protected $slug_from_key = 'name';

    /**
     * Get the user that owns the blog.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function directory()
    {
        return $this->belongsTo(Directory::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function keywords()
    {
        return $this->morphMany(Keyword::class, 'keywordable');
    }

    /**
     * Filter the latest entries
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Filter by Directory
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeDirectory($query, $value)
    {
        return $query->where('directory_id', $value);
    }

    /**
     * @return mixed
     */
    public function nextPost()
    {
        return self::select('image', 'name', 'description', 'slug', 'created_at')
            ->where('id', '<', $this->id)
            ->directory(env('DIRECTORY_ID'))
            ->orderBy('id', 'asc')
            ->first();
    }

    /**
     * @return mixed
     */
    public function previousPost()
    {
        return self::select('image', 'name', 'description', 'slug', 'created_at')
            ->where('id', '>', $this->id)
            ->directory(env('DIRECTORY_ID'))
            ->orderBy('id', 'asc')
            ->first();
    }
}
