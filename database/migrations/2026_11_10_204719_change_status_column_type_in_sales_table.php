<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Menggunakan Statement SQL mentah agar bypass kelemahan DBAL pada ENUM
        DB::statement("ALTER TABLE sales MODIFY COLUMN status VARCHAR(20) NOT NULL DEFAULT 'PAID'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE sales MODIFY COLUMN status VARCHAR(255) NOT NULL DEFAULT 'PAID'");
    }
};