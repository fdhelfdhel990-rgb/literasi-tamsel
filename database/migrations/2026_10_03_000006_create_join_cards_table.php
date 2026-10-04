<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('join_cards', function (Blueprint $table): void {
            $table->id();
            $table->string('key', 40)->unique();
            $table->string('title');
            $table->text('description');
            $table->string('form_url', 2048)->nullable();
            $table->boolean('is_open')->default(false);
            $table->text('closed_description')->nullable();
            $table->string('button_label')->default('Daftar');
            $table->unsignedInteger('position')->default(0)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('join_cards');
    }
};