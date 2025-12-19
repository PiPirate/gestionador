<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('liquidations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investor_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->decimal('amount_usd', 12, 2);
            $table->decimal('monthly_rate', 5, 2)->default(0);
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('gain_cop', 15, 2)->default(0);
            $table->decimal('total_cop', 15, 2)->default(0);
            $table->string('status')->default('pendiente');
            $table->date('due_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('liquidations');
    }
};
