<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductAudit extends Model
{
    use HasFactory;  // ← Standard factory support
    
    protected $fillable = [
        'product_id', 'before_stock', 'after_stock', 'action', 'user_id'
    ];
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
