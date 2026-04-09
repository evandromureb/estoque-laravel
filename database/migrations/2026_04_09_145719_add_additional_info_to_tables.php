<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->text('additional_info')->nullable();
        });
        Schema::table('warehouses', function (Blueprint $table): void {
            $table->text('additional_info')->nullable();
        });
        Schema::table('categories', function (Blueprint $table): void {
            $table->text('additional_info')->nullable();
        });
        Schema::table('products', function (Blueprint $table): void {
            $table->text('additional_info')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('additional_info');
        });
        Schema::table('warehouses', function (Blueprint $table): void {
            $table->dropColumn('additional_info');
        });
        Schema::table('categories', function (Blueprint $table): void {
            $table->dropColumn('additional_info');
        });
        Schema::table('products', function (Blueprint $table): void {
            $table->dropColumn('additional_info');
        });
    }
};
