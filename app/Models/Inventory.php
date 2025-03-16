<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_id',
        'category_id',
        'quantity',
        'stock_in',
        'stock_out',
        'sku',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
