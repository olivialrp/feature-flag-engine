<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('environment_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->unsignedBigInteger('request_count')->default(0);
            $table->timestamps();

            $table->unique(['environment_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usage_logs');
    }
};
