<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel transaksi keuangan — inti dari sistem (pemasukan dan pengeluaran)
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->enum('type', ['pemasukan', 'pengeluaran']);
            $table->decimal('amount', 15, 2);
            $table->string('description');
            $table->date('transaction_date');
            $table->string('reference_number')->nullable()->comment('No. faktur / kuitansi');
            $table->text('note')->nullable();
            $table->unsignedBigInteger('created_by')->comment('user_id yang menginput transaksi');
            $table->foreign('created_by')->references('id')->on('users')->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Index untuk performa query laporan
            $table->index(['transaction_date', 'type']);
            $table->index(['category_id', 'type']);
            $table->index('created_by');
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
