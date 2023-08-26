<?php

namespace App\Models;

use App\Models\Traits\LogActions;
use App\Notifications\NewPasswordNotification;
use App\Notifications\ResetPasswordNotification;
use Helper\Common;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class Account extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes, LogActions;

    public const DEFAULT_ORDER_SCOPE = 'defaultOrder';

    protected string $orderByColumn = 'id';

    protected string $orderByDirection = 'desc';

    protected $table = 'accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token',
        'password'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected static function booting()
    {
        static::addGlobalScope(
            self::DEFAULT_ORDER_SCOPE,
            function (Builder $builder) {
                $builder->scopes('defaultOrderBy');
            }
        );
    }

    public function scopeSearch(Builder $query, $search): Builder
    {
        return $query->where(function ($query) use ($search) {
            $query->where('name', 'like', '%' . Common::escapeWildcard($search) . '%')
                ->orWhere('email', 'like', '%' . Common::escapeWildcard($search) . '%');
        });
    }

    public function scopeName(Builder $query, $name): Builder
    {
        return $query->where('name', 'like', '%' . Common::escapeWildcard($name) . '%');
    }

    public function scopeEmail(Builder $query, $email): Builder
    {
        return $query->where('email', 'like', '%' . Common::escapeWildcard($email) . '%');
    }

    /**
     * Scope a query to set a default order.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeDefaultOrderBy(Builder $query): Builder
    {
        if (!$this->timestamps) {
            return $query;
        }

        return $query->orderBy($this->getTable() . '.' . $this->orderByColumn, $this->orderByDirection);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token, $this));
    }

    /**
     * Send the new password notification.
     *
     * @param string $token
     * @return void
     */
    public function sendNewPasswordNotification($token)
    {
        $this->notify(new NewPasswordNotification($token, $this));
    }

    /**
     * Hash password
     *
     * @param $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }
}
