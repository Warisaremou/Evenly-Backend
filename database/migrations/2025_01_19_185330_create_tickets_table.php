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
        Schema::create('tickets', function (Blueprint $table) {
            $table->uuid('id')->primary(); 
            $table->string('name');
            $table->integer('quantity'); 
            $table->decimal('price', 8, 2); 
            $table->uuid('event_id'); 
            $table->uuid('user_id'); 
            $table->uuid('typeticket_id'); 
            $table->timestamps(); 
            $table->softDeletes();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('typeticket_id')->references('id')->on('typetickets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
