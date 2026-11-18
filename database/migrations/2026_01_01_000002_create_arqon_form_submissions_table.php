<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('arqon_form_submissions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('form_id')->constrained('arqon_forms')->cascadeOnDelete();
            $table->json('data');
            $table->json('field_snapshot')->nullable();
            $table->string('status', 30)->default('new')->index();
            $table->string('ip_hash', 64)->nullable()->index();
            $table->string('source_url', 2048)->nullable();
            $table->timestamp('submitted_at')->useCurrent()->index();
            $table->timestamp('handled_at')->nullable();
            $table->timestamps();
            $table->index(['form_id', 'submitted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arqon_form_submissions');
    }
};
