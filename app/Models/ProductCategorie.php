<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategorie extends Model
{
    use SoftDeletes;
    
    protected $table = 'product_categories';

    protected $fillable = [
        'nama_kategori',
        'parent_id',
        'deskripsi',
    ];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
