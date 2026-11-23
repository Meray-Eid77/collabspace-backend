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
       Schema::create('meeting_transcripts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('meeting_id')->constrained()->cascadeOnDelete();
    $table->foreignId('speaker_id')->nullable()->constrained('users')->nullOnDelete();
    $table->string('timestamp', 10)->nullable(); // e.g. 00:01
    $table->text('text');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_transcripts');
    }
};
