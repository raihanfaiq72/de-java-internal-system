<?php

namespace App\Models;

use App\Traits\ActivityLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use ActivityLogs, SoftDeletes;

    protected $table = 'invoices';

    protected $fillable = [
        'office_id',
        'tipe_invoice',
        'is_kop',
        'logo_img',
        'nomor_invoice',
        'tgl_invoice',
        'tgl_jatuh_tempo',
        'ref_no',
        'mitra_id',
        'kontak_person_id',
        'sales_id',
        'status_dok',
        'status_pembayaran',
        'status_perjalanan',
        'subtotal',
        'total_diskon_item',
        'diskon_tambahan_nilai',
        'diskon_tambahan_tipe',
        'biaya_kirim',
        'uang_muka',
        'total_akhir',
        'keterangan',
        'syarat_ketentuan',
    ];

    public function mitra()
    {
        return $this->belongsTo(Partner::class);
    }

    public function sales()
    {
        return $this->belongsTo(User::class, 'sales_id');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payment()
    {
        return $this->hasMany(Payment::class);
    }

    public function deliveryOrderInvoices()
    {
        return $this->hasMany(DeliveryOrderInvoice::class);
    }

    public function activities()
    {
        return $this->hasMany(ActivityLog::class, 'data_id')->where('tabel_terkait', $this->getTable())->latest();
    }

    public function approvals()
    {
        return $this->hasMany(InvoiceApprovalDetail::class);
    }
}
