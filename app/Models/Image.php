<?php

namespace App\Models;

use App\Traits\ImageHelper;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
	use ImageHelper;

	const GALLERY_IMAGES_LIMIT = 5;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'image',
        'title'
	];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\MorphTo
	 */
	public function imageable()
	{
		return $this->morphTo();
	}
}
