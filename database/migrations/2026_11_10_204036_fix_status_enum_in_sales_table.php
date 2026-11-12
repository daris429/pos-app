<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Ubah tipe data kolom status menjadi string VARCHAR(20) agar muat 'CANCELLED'
            $table->string('status', 20)->default('PAID')->change();
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('status', 255)->change();
        });
    }
};