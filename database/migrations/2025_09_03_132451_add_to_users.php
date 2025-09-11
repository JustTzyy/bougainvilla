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
        Schema::table('users', function (Blueprint $table) {

            $table->string('firstName');
            $table->string('middleName')->nullable();
            $table->string('lastName');
            $table->string('contactNumber');
            $table->string('birthday');
            $table->string('status');
            $table->string('age');
            $table->string('sex');
            $table->unsignedBigInteger('roleID');
            $table->foreign('roleID')->references('id')->on('roles')->onDelete('cascade');
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
