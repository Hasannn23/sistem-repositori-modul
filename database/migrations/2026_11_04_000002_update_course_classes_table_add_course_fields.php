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
        Schema::table('course_classes', function (Blueprint $table) {
            if (! Schema::hasColumn('course_classes', 'course_id')) {
                $table->foreignId('course_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('courses')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('course_classes', 'academic_year')) {
                $table->string('academic_year')->nullable()->after('referral_code');
            }

            if (! Schema::hasColumn('course_classes', 'semester')) {
                $table->string('semester')->nullable()->after('academic_year');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_classes', function (Blueprint $table) {
            if (Schema::hasColumn('course_classes', 'course_id')) {
                $table->dropForeign(['course_id']);
                $table->dropColumn('course_id');
            }

            if (Schema::hasColumn('course_classes', 'academic_year')) {
                $table->dropColumn('academic_year');
            }

            if (Schema::hasColumn('course_classes', 'semester')) {
                $table->dropColumn('semester');
            }
        });
    }
};

