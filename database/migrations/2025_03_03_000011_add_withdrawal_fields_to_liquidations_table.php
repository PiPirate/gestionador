<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('liquidations', function (Blueprint $table) {
            $table->foreignId('investment_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('withdrawn_gain_cop', 15, 2)->default(0);
            $table->decimal('withdrawn_capital_cop', 15, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('liquidations', function (Blueprint $table) {
            $table->dropForeign(['investment_id']);
            $table->dropColumn(['investment_id', 'withdrawn_gain_cop', 'withdrawn_capital_cop']);
        });
    }
};
