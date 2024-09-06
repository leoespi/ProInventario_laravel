<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // ID del producto
            $table->string('name'); // Nombre del producto
            $table->text('description')->nullable(); // Descripción opcional
            $table->decimal('price', 10, 2); // Precio del producto
            $table->integer('stock'); // Cantidad en stock
            $table->timestamps(); // Fechas de creación y actualización
        });
    }

    
    public function down()
    {
        Schema::dropIfExists('products');
    }
}