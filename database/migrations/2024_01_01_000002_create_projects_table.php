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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->enum('status', ['New', 'In Progress', 'On Hold', 'Completed', 'Closed'])->default('New');
            $table->integer('progress_percent')->default(0);
            $table->date('creation_date');
            $table->date('closing_date')->nullable();
            $table->string('contract', 512)->nullable();
            $table->text('description')->nullable();
            $table->decimal('estimated_time', 6, 2)->nullable();
            $table->decimal('spent_time', 6, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
