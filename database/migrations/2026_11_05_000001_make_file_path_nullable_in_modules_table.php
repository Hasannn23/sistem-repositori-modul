<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * LMS-type modules do not require a file_path (content is stored in module_meetings/module_items).
     * Also make course_class_id nullable since sayembara modules may not belong to a specific class initially.
     */
    public function up(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->text('file_path')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->text('file_path')->nullable(false)->change();
        });
    }
};

