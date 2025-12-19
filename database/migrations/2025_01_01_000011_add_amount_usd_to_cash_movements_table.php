<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cash_movements', function (Blueprint $table) {
            $table->decimal('amount_usd', 15, 2)->default(0)->after('amount_cop');
            $table->decimal('balance_usd', 15, 2)->nullable()->after('balance_cop');
        });
    }

    public function down(): void
    {
        Schema::table('cash_movements', function (Blueprint $table) {
            $table->dropColumn(['amount_usd', 'balance_usd']);
        });
    }
};
