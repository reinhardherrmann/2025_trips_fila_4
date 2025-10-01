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
        Schema::create('shift_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // z.B. "F", "S", "N"
            $table->string('name'); // z.B. "Frühschicht"
            $table->time('start_time'); // z.B. 06:00
            $table->time('end_time'); // z.B. 14:00
            $table->integer('duration_minutes'); // Sollarbeitszeit in Minuten
            $table->integer('break_minutes')->default(0); // Pausenzeit
            $table->boolean('is_active')->default(true);
            $table->text('remark')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_types');
    }
};
