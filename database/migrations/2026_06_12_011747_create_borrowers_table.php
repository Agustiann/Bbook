<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrowers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users');
            $table->string('name');
            $table->string('phone', 20);
            $table->text('address');
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users');
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowers');
    }
};
