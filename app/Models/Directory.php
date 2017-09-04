<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Directory extends Model
{
	public $timestamps = false;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
        'api_token'
	];

    /**
     * @return string
     */
    public static function generateToken()
    {
        return str_random(60);
	}
}
