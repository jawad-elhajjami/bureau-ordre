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
        Schema::create('documents', function (Blueprint $table) {
            $table->id('document_id');
            $table->integer('order_number');
            $table->string('subject', 255);
            $table->string('file_path', 255);
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected']);
            $table->enum('importance_level', ['low', 'medium', 'high']);
            $table->integer('opt_code');
            // $table->foreignId('submitted_by')->constrained('users');
            // $table->foreignId('department_id')->constrained('departments');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('category_id')->constrained('document_categories');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
