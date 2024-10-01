<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'total',
    ];

    public function productos()
    {
        return $this->belongsToMany(Product::class, 'venta_producto')
                    ->withPivot('cantidad')
                    ->withTimestamps();
    }
}
