<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            if (!Schema::hasColumn('investments', 'amount_cop')) {
                $table->decimal('amount_cop', 15, 2)->default(0)->after('code');
            }

            if (!Schema::hasColumn('investments', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
            }

            if (!Schema::hasColumn('investments', 'closed_at')) {
                $table->timestamp('closed_at')->nullable()->after('status');
            }

            if (Schema::hasColumn('investments', 'next_liquidation_date')) {
                $table->dropColumn('next_liquidation_date');
            }
        });

        if (Schema::hasColumn('investments', 'amount_usd')) {
            Schema::table('investments', function (Blueprint $table) {
                $table->dropColumn('amount_usd');
            });
        }
    }

    public function down(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            if (!Schema::hasColumn('investments', 'amount_usd')) {
                $table->decimal('amount_usd', 12, 2)->default(0)->after('code');
            }

            if (Schema::hasColumn('investments', 'amount_cop')) {
                $table->dropColumn('amount_cop');
            }

            if (Schema::hasColumn('investments', 'end_date')) {
                $table->dropColumn('end_date');
            }

            if (Schema::hasColumn('investments', 'closed_at')) {
                $table->dropColumn('closed_at');
            }

            if (!Schema::hasColumn('investments', 'next_liquidation_date')) {
                $table->date('next_liquidation_date')->nullable();
            }
        });
    }
};
