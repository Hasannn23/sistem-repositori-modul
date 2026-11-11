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
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE modules MODIFY COLUMN storage_type VARCHAR(50) NOT NULL DEFAULT 'lms'");
        } else {
            Schema::table('modules', function (Blueprint $table) {
                $table->string('storage_type', 50)->default('lms')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE modules MODIFY COLUMN storage_type ENUM('pdf', 'gdrive', 'link') NOT NULL");
        } else {
            Schema::table('modules', function (Blueprint $table) {
                $table->string('storage_type', 50)->default('pdf')->change();
            });
        }
    }
};

