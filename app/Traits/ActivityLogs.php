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
        $officeId = session('active_office_id') ?? ($model->office_id ?? null);

        // Fallback: Try to get office_id from relationships if not found
        if (!$officeId) {
            if ($model->invoice && $model->invoice->office_id) {
                $officeId = $model->invoice->office_id;
            } elseif ($model->office && $model->office->id) {
                $officeId = $model->office->id;
            } else {
                // Last resort fallback to prevent crash in queue jobs
                $officeId = 1; 
            }
        }

        ActivityLog::create([
            'office_id' => $officeId,
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
