<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('work_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('shift_type_id')->nullable()->constrained()->onDelete('set null');
            $table->date('work_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('actual_minutes')->nullable(); // Tatsächliche Arbeitszeit
            $table->integer('target_minutes')->nullable(); // Sollarbeitszeit
            $table->integer('difference_minutes')->nullable(); // Differenz (+ oder -)
            $table->text('remark')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['user_id', 'work_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_records');
    }
};
