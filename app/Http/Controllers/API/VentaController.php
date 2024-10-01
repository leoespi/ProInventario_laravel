<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Product;
use Illuminate\Support\Facades\DB;


class VentaController extends Controller
{
     // Registrar una nueva venta
     public function store(Request $request)
     {
         $request->validate([
             'productos' => 'required|array', // Un array de productos con cantidad
             'productos.*.id' => 'required|exists:products,id',
             'productos.*.cantidad' => 'required|integer|min:1',
         ]);
 
         DB::beginTransaction(); // Empezar la transacción para asegurar atomicidad
 
         try {
             // Crear una nueva venta
             $venta = Venta::create([
                 'total' => 0, // Lo calcularemos después
             ]);
 
             $totalVenta = 0;
 
             // Procesar cada producto
             foreach ($request->productos as $item) {
                 $product = Product::find($item['id']);
 
                 // Restar la cantidad del stock
                 if ($product->stock < $item['cantidad']) {
                     return response()->json(['error' => 'Stock insuficiente para el producto: ' . $product->name], 400);
                 }
 
                 $product->stock -= $item['cantidad'];
                 $product->save();
 
                 // Registrar el producto en la venta
                 $venta->productos()->attach($product->id, [
                     'cantidad' => $item['cantidad']
                 ]);
 
                 // Calcular el total de la venta
                 $totalVenta += $product->price * $item['cantidad'];
             }
 
             // Actualizar el total en la venta
             $venta->total = $totalVenta;
             $venta->save();
 
             DB::commit(); // Confirmar la transacción
 
             return response()->json([
                 'message' => 'Venta registrada con éxito.',
                 'venta' => $venta->load('productos') // Cargar productos asociados
             ], 201);
         } catch (\Exception $e) {
             DB::rollBack(); // Revertir la transacción en caso de error
             return response()->json(['error' => 'Error al registrar la venta'], 500);
         }
     }
}
