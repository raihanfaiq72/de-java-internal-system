<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\BulkReport;
use Illuminate\Support\Str;

class TestBulkReportNotifications extends Command
{
    protected $signature = 'test:bulk-notifications';
    protected $description = 'Create test bulk report notifications';

    public function handle()
    {
        $user = User::first();
        $bulkReport = BulkReport::first();

        if (!$user) {
            $this->error('No user found');
            return;
        }

        if (!$bulkReport) {
            $this->error('No bulk report found');
            return;
        }

        // Create bulk report created notification
        $user->notifications()->create([
            'id' => Str::uuid(),
            'type' => 'success',
            'data' => [
                'type' => 'bulk_report_created',
                'title' => 'Laporan Massal Baru',
                'message' => 'Periode ' . $bulkReport->period_name . ' telah ditambahkan ke daftar laporan massal.',
                'bulk_report_id' => $bulkReport->id,
                'period_name' => $bulkReport->period_name,
                'created_by' => 'System'
            ]
        ]);

        // Create bulk report generated notification
        $user->notifications()->create([
            'id' => Str::uuid(),
            'type' => 'info',
            'data' => [
                'type' => 'bulk_report_generated',
                'title' => 'PDF Laporan Massal Tersedia',
                'message' => 'PDF laporan massal untuk periode ' . $bulkReport->period_name . ' telah siap diunduh.',
                'bulk_report_id' => $bulkReport->id,
                'period_name' => $bulkReport->period_name,
                'generated_by' => 'System'
            ]
        ]);

        // Create bulk report printed notification
        $user->notifications()->create([
            'id' => Str::uuid(),
            'type' => 'warning',
            'data' => [
                'type' => 'bulk_report_printed',
                'title' => 'Laporan Massal Dicetak',
                'message' => 'Laporan massal untuk periode ' . $bulkReport->period_name . ' telah ditandai sebagai telah dicetak.',
                'bulk_report_id' => $bulkReport->id,
                'period_name' => $bulkReport->period_name,
                'marked_by' => 'System'
            ]
        ]);

        $this->info('Bulk report notifications created successfully!');
        $this->info('Total notifications for user: ' . $user->notifications()->count());
    }
}
