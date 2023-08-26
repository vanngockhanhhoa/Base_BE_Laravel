<?php

namespace App\Models;

use App\Models\Traits\HasFillable;
use App\Models\Traits\WithTable;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseModel extends Model
{
    use WithTable;
    use HasFillable;
    use HasFactory;
    use SoftDeletes;

    public const DEFAULT_ORDER_SCOPE = 'defaultOrder';

    protected string $orderByColumn = 'id';

    protected string $orderByDirection = 'desc';

//    protected $hidden = ['id', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return Carbon::parse($date);
    }

    /**
     * The "booting" method of the model.
     */
    protected static function booting(): void
    {
        static::addGlobalScope(
            self::DEFAULT_ORDER_SCOPE,
            function (Builder $builder) {
                $builder->scopes('defaultOrderBy');
            }
        );
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

//    /**
//     * Retrieve the model for a bound value.
//     *
//     * @return string
//     */
//    public function getRouteKeyName(): string
//    {
//        return 'id';
//    }
}
