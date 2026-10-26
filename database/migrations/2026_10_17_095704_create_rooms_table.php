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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number')->unique();
            $table->string('type');                       // Standard, Deluxe, Suite
            $table->decimal('price', 8, 2);               // តម្លៃមួយយប់
            $table->unsignedTinyInteger('capacity')->default(2);
            $table->string('status')->default('available'); // available, occupied, cleaning, maintenance
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
