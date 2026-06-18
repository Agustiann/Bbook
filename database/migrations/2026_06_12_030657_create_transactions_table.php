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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('borrower_id')
                ->constrained('borrowers');

            $table->foreignId('book_id')
                ->constrained('books');

            $table->foreignId('user_id')
                ->constrained('users');

            $table->date('borrowed_at');

            $table->date('returned_at')
                ->nullable();

            $table->date('due_date');

            $table->integer('late_days')
                ->default(0);

            $table->decimal('total_fine', 12, 2)
                ->default(0);

            $table->enum('status', [
                'borrowed',
                'extended',
                'returned',
                'late',
            ])->default('borrowed');
            
            $table->integer('extension_count')
                ->default(0);

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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
