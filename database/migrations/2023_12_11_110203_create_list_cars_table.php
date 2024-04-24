<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('list_cars', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('name');
            $table->string('image');
            $table->string('address');
            $table->string('make');
            $table->string('model');
            $table->integer('year');
            $table->string('mileage');
            $table->string('fuel');
            $table->string('documents');
            $table->string('trnasmission');
            $table->string('condition');
            $table->string('color');
            $table->decimal('price', 11, 3);
            $table->string('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('list_cars');
    }
};
