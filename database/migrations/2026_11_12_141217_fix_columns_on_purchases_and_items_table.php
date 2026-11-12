<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Lengkapi semua kolom tabel purchases
        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases', 'reference_no')) {
                $table->string('reference_no')->unique()->after('id');
            }
            if (!Schema::hasColumn('purchases', 'supplier_id')) {
                $table->foreignId('supplier_id')->after('reference_no')->constrained()->cascadeOnDelete();
            }
            if (!Schema::hasColumn('purchases', 'user_id')) {
                $table->foreignId('user_id')->after('supplier_id')->constrained()->cascadeOnDelete();
            }
            if (!Schema::hasColumn('purchases', 'purchase_date')) {
                $table->date('purchase_date')->after('user_id');
            }
            if (!Schema::hasColumn('purchases', 'total_amount')) {
                $table->decimal('total_amount', 15, 2)->default(0)->after('purchase_date');
            }
            if (!Schema::hasColumn('purchases', 'notes')) {
                $table->text('notes')->nullable()->after('total_amount');
            }
        });

        // 2. Sekaligus pastikan kolom tabel purchase_items juga lengkap
        Schema::table('purchase_items', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_items', 'purchase_id')) {
                $table->foreignId('purchase_id')->after('id')->constrained()->cascadeOnDelete();
            }
            if (!Schema::hasColumn('purchase_items', 'product_id')) {
                $table->foreignId('product_id')->after('purchase_id')->constrained()->cascadeOnDelete();
            }
            if (!Schema::hasColumn('purchase_items', 'quantity')) {
                $table->integer('quantity')->after('product_id');
            }
            if (!Schema::hasColumn('purchase_items', 'purchase_price')) {
                $table->decimal('purchase_price', 15, 2)->after('quantity');
            }
            if (!Schema::hasColumn('purchase_items', 'subtotal')) {
                $table->decimal('subtotal', 15, 2)->after('purchase_price');
            }
        });
    }

    public function down(): void
    {
        //
    }
};