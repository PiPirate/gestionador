<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('profit_rules', function (Blueprint $table) {
            $table->id();
            $table->json('tiers_json');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::table('investments', function (Blueprint $table) {
            $table->foreignId('profit_rule_id')->nullable()->after('investor_id')->constrained()->nullOnDelete();
            $table->json('tiers_snapshot')->nullable()->after('monthly_rate');
            $table->decimal('monthly_profit_snapshot', 15, 2)->default(0)->after('tiers_snapshot');
            $table->decimal('daily_interest_snapshot', 15, 6)->default(0)->after('monthly_profit_snapshot');
        });
    }

    public function down(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            $table->dropForeign(['profit_rule_id']);
            $table->dropColumn([
                'profit_rule_id',
                'tiers_snapshot',
                'monthly_profit_snapshot',
                'daily_interest_snapshot',
            ]);
        });

        Schema::dropIfExists('profit_rules');
    }
};
