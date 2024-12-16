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
        Schema::table('vouchers', function (Blueprint $table) {
            $table->string('invoice_series');
            $table->string('number');
            $table->enum('invoice_type', ['01', '03','07','08']);
            $table->enum('currency', ['PEN', 'USD']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn(['invoice_series', 'number', 'invoice_type', 'currency']);
        });
    }
};
