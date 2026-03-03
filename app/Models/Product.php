<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Product extends Model {
    protected $fillable = ['name', 'sku', 'stock', 'price'];
    
    public function audits() { return $this->hasMany(ProductAudit::class); }
    
    public function adjustStock($quantity, $userId = null) {
        if ($quantity < 0 && $this->stock < abs($quantity)) {
            throw new \Exception("Insufficient stock: {$this->stock} < " . abs($quantity));
        }
        $before = $this->stock;
        $this->stock += $quantity;
        $this->save();
        ProductAudit::create([
            'product_id' => $this->id, 'before_stock' => $before, 'after_stock' => $this->stock,
            'action' => $quantity > 0 ? 'add' : 'sale', 'user_id' => $userId
        ]);
    }
}

