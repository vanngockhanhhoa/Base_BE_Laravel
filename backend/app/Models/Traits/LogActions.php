<?php

namespace App\Models\Traits;

use App\Models\ActionLog;
use Illuminate\Support\Facades\Log;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

trait LogActions
{
    /**
     * Boot the Model.
     */

    public static function boot()
    {
        parent::boot();
        try {
            $account = JWTAuth::user();

            static::created(function ($model) use ($account) {
                if ($account && !isset($model->prevent_logging)) {
                    ActionLog::create([
                        'object_id' => $model->id,
                        'owner_id' => $account ? $account->id : null,
                        'action' => LOG_ACTIONS['CREATING'],
                        'info' => null,
                        'before' => null,
                        'before_changes' => null,
                        'after' => $model->toArray(),
                        'after_changes' => $model->toArray(),
                    ]);
                }
            });

            static::updating(function ($model) use ($account) {
                if ($account && !isset($model->prevent_logging) && $model->isDirty()) {
                    $originalChanges = [];
                    foreach ($model->getDirty() as $field => $value) {
                        $originalChanges[$field] = $model->original[$field] ?? null;
                    }
                    ActionLog::create([
                        'object_id' => $model->id,
                        'owner_id' => $account ? $account->id : null,
                        'action' => LOG_ACTIONS['UPDATING'],
                        'info' => $model->getLogInfo() ?? null,
                        'before' => $model->original,
                        'after' => $model->toArray(),
                        'before_changes' => $originalChanges,
                        'after_changes' => $model->getDirty(),
                    ]);
                }
            });

            static::deleted(function ($model) use ($account) {
                if ($account && !isset($model->prevent_logging) && $model->isDirty()) {
                    ActionLog::create([
                        'object_id' => $model->id,
                        'owner_id' => $account ? $account->id : null,
                        'action' => LOG_ACTIONS['DELETING'],
                        'info' => $model->getLogInfo() ?? null,
                        'before' => $model->toArray(),
                        'after' => [],
                        'before_changes' => [],
                        'after_changes' => [],
                    ]);
                }
            });
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }

    public function getLogInfo()
    {
        return null;
    }
}
