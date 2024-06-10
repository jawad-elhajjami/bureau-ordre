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
            $table->id();
            $table->string('order_number');
            $table->unique('order_number');
            $table->string('subject', 255);
            $table->string('file_path', 255);
            $table->text('description')->nullable();
            $table->enum('importance_level', ['low', 'medium', 'high'])->nullable();
            $table->integer('opt_code')->nullable();
            $table->foreignId('service_id')->constrained('services');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('category_id')->constrained('document_categories');
            $table->unsignedBigInteger('recipient_id')->nullable()->default(null);
            $table->foreign('recipient_id')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['recipient_id']);
            $table->dropColumn('recipient_id');
        });
    }
};
