<?php

namespace App\Models;

use App\Traits\Roles;
use Cmgmyr\Messenger\Traits\Messagable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Contracts\UserResolver;

class User extends Authenticatable implements AuditableContract, UserResolver
{
    use Notifiable, Roles, SoftDeletes, Messagable, Auditable;

    const CLIENT_NUMBER_START = 1000;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
        'client_number',
        'gender',
        'title',
        'first_name',
        'last_name',
        'phone_number',
        'role'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'deleted_at'
    ];

    /**
     * Attributes to exclude from the Audit. Timestamps are excluded by default (see setting auditTimestamps)
     *
     * @var array
     */
    protected $auditExclude = [
        'remember_token',
        'password'
    ];

    /**
     * Should the timestamps be audited?
     *
     * @var bool
     */
    protected $auditTimestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    /**
     * @return mixed|null
     */
    public static function resolveId()
    {
        return auth()->check() ? auth()->user()->getAuthIdentifier() : null;
    }

    /**
     * @return mixed
     */
    public static function generateClientNumber()
    {
        return self::CLIENT_NUMBER_START + self::where('role', 'customer')->count();
    }

    /**
     * @return string
     */
    public function getNameAttribute()
    {
        return ($this->title ? $this->title . ' ' : '') . $this->first_name . ' ' . $this->last_name;
    }

    /**
     * @return mixed
     */
    public function getCreatedDateAttribute()
    {
        return $this->created_at->format('d.m.Y');
    }
}
