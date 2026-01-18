<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $table = 'invoices';

    protected $fillable = [
        'office_id',
        'tipe_invoice',
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
        'perlu_acc_admin',
    ];

    public function mitra()
    {
        return $this->belongsTo(Mitra::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payment()
    {
        return $this->hasMany(Payment::class);
    }
}