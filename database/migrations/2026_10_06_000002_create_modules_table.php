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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_class_id')->constrained('course_classes')->onDelete('cascade');
            $table->string('title');
            $table->enum('storage_type', ['pdf', 'gdrive']);
            $table->string('file_path');
            $table->boolean('is_locked')->default(true);
            $table->enum('status', ['draft', 'waiting_review', 'revision', 'approved'])->default('draft');
            $table->text('kajur_notes')->nullable();
            $table->foreignId('kajur_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
