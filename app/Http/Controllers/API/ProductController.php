<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;


class ProductController extends Controller
{
    // Muestra una lista de todos los productos
    public function index()
    {
        $products = Product::all();
        return response(['products' => $products 
    ], 200,[],JSON_NUMERIC_CHECK);
      
    }




    // Guarda un nuevo producto en la base de datos
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
        ]);

        Product::create($request->all());

        return redirect()->route('products.index')->with('success', 'Producto creado con éxito.');
    }


  
    // Actualiza un producto existente en la base de datos
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
        ]);

        $product->update($request->all());

        return redirect()->route('products.index')->with('success', 'Producto actualizado con éxito.');
    }


    // Elimina un producto específico de la base de datos
    public function destroy(Product $product)
    {
        $product->delete();
    }
}
