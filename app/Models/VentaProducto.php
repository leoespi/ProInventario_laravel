<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentaProducto extends Model
{
    use HasFactory;

    protected $table = 'venta_producto';

    protected $fillable = [
        'venta_id',
        'product_id',
        'cantidad',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
}
