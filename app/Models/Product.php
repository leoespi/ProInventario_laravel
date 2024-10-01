<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Definir los atributos que se pueden llenar masivamente
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
    ];

    // Opcional: si quieres deshabilitar las marcas de tiempo (created_at y updated_at)
    // public $timestamps = false;

    public function ventas()
    {
        return $this->belongsToMany(Venta::class, 'venta_producto')
                    ->withPivot('cantidad')
                    ->withTimestamps();
    }
}
