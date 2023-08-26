<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class ActionLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'action_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'object_id',
        'owner_id',
        'action',
        'info',
        'before',
        'before_changes',
        'after',
        'after_changes'
    ];

    /**
     * Adding the array cast to that attribute will automatically deserialize the attribute to a PHP array when you access it on your Eloquent model.
     */
    protected $casts = [
        'before' => 'array',
        'before_changes' => 'array',
        'after' => 'array',
        'after_changes' => 'array',
    ];
}
