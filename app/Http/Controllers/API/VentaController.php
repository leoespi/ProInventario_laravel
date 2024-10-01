<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Product;
use Illuminate\Support\Facades\DB;


class VentaController extends Controller
{


    public function index()
    {
        // Obtener todas las ventas con los productos relacionados y los datos de cliente en la tabla intermedia
        $ventas = Venta::with(['productos' => function ($query) {
            $query->select('products.*', 'venta_producto.cantidad', 'venta_producto.nombre', 'venta_producto.telefono');
        }])->get();

        return response()->json([
            'ventas' => $ventas
        ], 200);
    }


    // Registrar una nueva venta
    public function store(Request $request)
    {
        $request->validate([
            'productos' => 'required|array', // Un array de productos con cantidad
            'productos.*.id' => 'required|exists:products,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'nombre' => 'required|string|max:255', // Validar el nombre del cliente
            'telefono' => 'required|string|max:15', // Validar el teléfono del cliente
        ]);
    
        DB::beginTransaction(); // Empezar la transacción para asegurar atomicidad
    
        try {
            // Crear una nueva venta
            $venta = Venta::create([
                'total' => 0, // Lo calcularemos después
            ]);
    
            $totalVenta = 0;
            $productosVendidos = []; // Array para almacenar los productos vendidos
    
            // Procesar cada producto
            foreach ($request->productos as $item) {
                $product = Product::find($item['id']);
    
                // Restar la cantidad del stock
                if ($product->stock < $item['cantidad']) {
                    return response()->json(['error' => 'Stock insuficiente para el producto: ' . $product->name], 400);
                }
    
                $product->stock -= $item['cantidad'];
                $product->save();
    
                // Registrar el producto en la venta con nombre y teléfono en la tabla intermedia
                DB::table('venta_producto')->insert([
                    'venta_id' => $venta->id,
                    'product_id' => $product->id,
                    'cantidad' => $item['cantidad'],
                    'nombre' => $request->nombre, // Nombre del cliente
                    'telefono' => $request->telefono, // Teléfono del cliente
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
    
                // Añadir el producto vendido al array
                $productosVendidos[] = [
                    'producto' => $product->name,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $product->price,
                    'subtotal' => $product->price * $item['cantidad']
                ];
    
                // Calcular el total de la venta
                $totalVenta += $product->price * $item['cantidad'];
            }
    
            // Actualizar el total en la venta
            $venta->total = $totalVenta;
            $venta->save();
    
            DB::commit(); // Confirmar la transacción
    
            // Devolver la "factura" como respuesta
            return response()->json([
                'message' => 'Venta registrada con éxito.',
                'factura' => [
                    'cliente' => [
                        'nombre' => $request->nombre,
                        'telefono' => $request->telefono
                    ],
                    'productos' => $productosVendidos,
                    'total' => $totalVenta
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Revertir la transacción en caso de error
            return response()->json(['error' => 'Error al registrar la venta'], 500);
        }
    }
    
}
