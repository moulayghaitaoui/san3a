<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teacher_attendances', function (Blueprint $table) {
            $table->dropForeign(['upload_log_id']);
            $table->unsignedBigInteger('upload_log_id')->nullable()->change();
            $table->foreign('upload_log_id')->references('id')->on('upload_logs')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('teacher_attendances', function (Blueprint $table) {
            $table->dropForeign(['upload_log_id']);
            $table->unsignedBigInteger('upload_log_id')->nullable(false)->change();
            $table->foreign('upload_log_id')->references('id')->on('upload_logs')->cascadeOnDelete();
        });
    }
};
