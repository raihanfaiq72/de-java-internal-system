<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    /**
     * Record user activity log
     *
     * @param string $tindakan Action name (create, update, delete, etc)
     * @param string $tabelTerkait Related table name
     * @param int|string $dataId ID of the related data
     * @param array|null $dataSebelum Data before change
     * @param array|null $dataSesudah Data after change
     * @return ActivityLog
     */
    public function logActivity($tindakan, $tabelTerkait, $dataId, $dataSebelum = null, $dataSesudah = null)
    {
        return ActivityLog::create([
            'office_id'     => session('active_office_id'),
            'user_id'       => Auth::id() ?? 1,
            'tindakan'      => $tindakan,
            'tabel_terkait' => $tabelTerkait,
            'data_id'       => $dataId,
            'data_sebelum'  => $dataSebelum,
            'data_sesudah'  => $dataSesudah,
            'ip_address'    => request()->ip(),
        ]);
    }
}
