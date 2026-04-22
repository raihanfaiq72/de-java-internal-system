<?php

namespace App\Models;

use App\Traits\ActivityLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialAccount extends Model
{
    use ActivityLogs, SoftDeletes;

    protected $guarded = ['id'];

    public function payments()
    {
        return $this->hasMany(Payment::class, 'akun_keuangan_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'akun_keuangan_id');
    }

    public function transfersIn()
    {
        return $this->hasMany(FinancialTransaction::class, 'to_account_id');
    }

    public function transfersOut()
    {
        return $this->hasMany(FinancialTransaction::class, 'from_account_id');
    }

    public function deliveryOrderCosts()
    {
        return $this->hasMany(DeliveryOrderInvoice::class, 'chart_of_accounts_id');
    }
}
