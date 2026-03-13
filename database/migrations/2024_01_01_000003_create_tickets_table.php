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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['New', 'In Progress', 'On Hold', 'Completed', 'Closed'])->default('New');
            $table->enum('priority', ['High', 'Medium', 'Low'])->default('Medium');
            $table->enum('type', ['Billed', 'Included'])->default('Billed');
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
        Schema::dropIfExists('tickets');
    }
};
