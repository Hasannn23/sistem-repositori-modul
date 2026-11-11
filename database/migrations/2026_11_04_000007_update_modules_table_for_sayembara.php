<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            if (! Schema::hasColumn('modules', 'sayembara_id')) {
                $table->foreignId('sayembara_id')
                    ->nullable()
                    ->after('course_id')
                    ->constrained('sayembaras')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('modules', 'is_master')) {
                $table->boolean('is_master')
                    ->default(false)
                    ->after('is_locked');
            }

            $table->foreignId('course_class_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            if (Schema::hasColumn('modules', 'sayembara_id')) {
                $table->dropForeign(['sayembara_id']);
                $table->dropColumn('sayembara_id');
            }

            if (Schema::hasColumn('modules', 'is_master')) {
                $table->dropColumn('is_master');
            }
        });
    }
};

