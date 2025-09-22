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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 10, 2);          // Total amount including tax
            $table->decimal('tax', 10, 2)->default(0); // Tax amount
            $table->decimal('subtotal', 10, 2);        // Amount before tax
            $table->string('status')->default('Pending');
            $table->decimal('change', 10, 2)->default(0);
            $table->foreignId('stayID')->constrained('stays')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
