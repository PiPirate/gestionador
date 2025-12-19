<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->decimal('amount_usd', 12, 2);
            $table->decimal('rate', 12, 2);
            $table->decimal('amount_cop', 15, 2);
            $table->string('counterparty')->nullable();
            $table->string('method')->nullable();
            $table->decimal('profit_cop', 15, 2)->default(0);
            $table->date('transacted_at');
            $table->string('reference')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
