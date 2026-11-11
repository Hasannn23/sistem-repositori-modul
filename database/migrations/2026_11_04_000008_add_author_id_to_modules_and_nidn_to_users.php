<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'nidn')) {
                $table->string('nidn', 50)->nullable()->after('email');
            }
        });

        Schema::table('modules', function (Blueprint $table) {
            if (! Schema::hasColumn('modules', 'author_id')) {
                $table->foreignId('author_id')
                    ->nullable()
                    ->after('sayembara_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });

        // Backfill author_id from course_classes.dosen_id if available
        if (Schema::hasTable('course_classes') && Schema::hasTable('modules')) {
            $courseClasses = DB::table('course_classes')->select('id', 'dosen_id')->get();
            foreach ($courseClasses as $class) {
                if ($class->dosen_id) {
                    DB::table('modules')
                        ->where('course_class_id', $class->id)
                        ->whereNull('author_id')
                        ->update(['author_id' => $class->dosen_id]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            if (Schema::hasColumn('modules', 'author_id')) {
                $table->dropForeign(['author_id']);
                $table->dropColumn('author_id');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'nidn')) {
                $table->dropColumn('nidn');
            }
        });
    }
};

