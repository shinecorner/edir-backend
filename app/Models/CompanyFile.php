<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyFile extends Model
{
	const FILE_LIMIT = 5;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
        'size',
        'mime'
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
	 * @return \Illuminate\Database\Eloquent\Relations\belongsTo
	 */
	public function company()
	{
		return $this->belongsTo(Company::class);
	}

    /**
     * @return null|string
     */
	public function download()
	{
	    $publicFile = null;

		if (isset($this->name)) {
			if(config('filesystems.disks.s3.serve')) {
                $publicFile = config('edir.aws_url') . $this->name;
            } else {
                $publicFile = '/storage/' . $this->name;
            }
		}

		return $publicFile;
	}
}
