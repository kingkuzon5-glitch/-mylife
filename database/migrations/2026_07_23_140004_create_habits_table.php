<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('habits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->default('repeat');
            $table->string('tracking_type')->default('boolean');
            $table->decimal('target_value', 8, 2)->nullable();
            $table->string('target_unit')->nullable();
            $table->time('target_time')->nullable();
            $table->string('schedule_type')->default('daily');
            $table->json('schedule_days')->nullable();
            $table->unsignedInteger('schedule_times_per_week')->nullable();
            $table->string('priority')->default('medium');
            $table->time('reminder_time')->nullable();
            $table->boolean('is_mandatory')->default(true);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedInteger('current_streak')->default(0);
            $table->unsignedInteger('best_streak')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('habits');
    }
};
