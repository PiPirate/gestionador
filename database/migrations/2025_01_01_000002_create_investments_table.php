<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investor_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->decimal('amount_usd', 12, 2);
            $table->decimal('monthly_rate', 5, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->decimal('gains_cop', 15, 2)->default(0);
            $table->date('next_liquidation_date')->nullable();
            $table->string('status')->default('activa');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investments');
    }
};
