<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            $table->decimal('amount_cop', 15, 2)->default(0)->after('code');
            $table->date('end_date')->nullable()->after('start_date');
            $table->timestamp('closed_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            $table->dropColumn(['amount_cop', 'end_date', 'closed_at']);
        });
    }
};
