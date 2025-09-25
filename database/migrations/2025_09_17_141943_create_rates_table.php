<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('rates', function (Blueprint $table) {
            $table->id();
            $table->string('duration');
            $table->decimal('price', 10, 2);
            $table->enum('status', ['Standard', 'Extending', 'Extending/Standard'])->default('Standard');            ;
            $table->foreignId('accommodation_id')->constrained('accommodations')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('rates');
    }
};
