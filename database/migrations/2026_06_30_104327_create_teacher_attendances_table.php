<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('upload_log_id')->constrained()->cascadeOnDelete();
            $table->foreignId('state_id')->constrained()->cascadeOnDelete();
            $table->string('reference_number')->nullable();
            $table->string('full_name');
            $table->string('status_type')->nullable();
            $table->string('specialty')->nullable();
            $table->string('rank')->nullable();
            $table->string('institution')->nullable();
            $table->boolean('is_present')->default(false);
            $table->boolean('is_absent')->default(false);
            $table->string('absence_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_attendances');
    }
};
