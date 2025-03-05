<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['author_id', 'name', 'category_id', 'size_id', 'product_no', 'price', 'color_id', 'image', 'status'];

    protected static function boot()
    {
        parent::boot();
        static::saving( function ($model) {
            $model ->created_at = date('Y-m-d H:i:s');
        });

        static::saving( function ($model) {
            $model ->updated_at = date('Y-m-d H:i:s');
        });
    }

    public function getImageAttribute($value)
    {
        return (!is_null($value)) ? env('APP_URL') . '/storage/' . $value : null;
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    public function size()
    {
        return $this->belongsTo(Size::class);
    }
}
