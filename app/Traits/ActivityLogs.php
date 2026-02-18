<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait ActivityLogs
{
    public static function bootActivityLogs()
    {
        static::created(function ($model) {
            self::logModelActivity('Create', $model, null, $model->toArray());
        });

        static::updated(function ($model) {
            self::logModelActivity('Update', $model, $model->getOriginal(), $model->getChanges());
        });

        static::deleted(function ($model) {
            self::logModelActivity('Soft Delete', $model, $model->toArray(), null);
        });
    }

    protected static function logModelActivity($action, $model, $before, $after)
    {
        ActivityLog::create([
            'office_id' => session('active_office_id') ?? $model->office_id ?? null,
            'user_id' => auth()->id() ?? 1,
            'tindakan' => $action,
            'tabel_terkait' => $model->getTable(),
            'data_id' => $model->getKey(),
            'data_sebelum' => $before,
            'data_sesudah' => $after,
            'ip_address' => request()->ip(),
        ]);
    }
}
