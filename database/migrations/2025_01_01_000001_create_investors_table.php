<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('investors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('document')->unique();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->date('since')->nullable();
            $table->string('status')->default('activo');
            $table->decimal('capital_usd', 12, 2)->default(0);
            $table->decimal('monthly_rate', 5, 2)->default(0);
            $table->decimal('gains_cop', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investors');
    }
};
