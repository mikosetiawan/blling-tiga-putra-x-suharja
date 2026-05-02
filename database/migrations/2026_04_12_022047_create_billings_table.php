<?php
// database/migrations/2024_01_01_000002_create_billings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billings', function (Blueprint $table) {
            $table->id();
            $table->string('no_invoice')->unique();
            $table->foreignId('pelanggan_id')->constrained('pelanggans')->onDelete('cascade');
            $table->date('tanggal_invoice');
            $table->date('jatuh_tempo');
            $table->date('tanggal_bayar')->nullable();
            $table->decimal('jumlah', 15, 2);
            $table->decimal('denda', 15, 2)->default(0);
            $table->decimal('total_bayar', 15, 2);
            $table->enum('status_bayar', ['belum_bayar', 'lunas', 'sebagian'])->default('belum_bayar');
            $table->string('metode_bayar')->nullable();
            $table->string('bukti_bayar')->nullable(); // path file
            $table->text('keterangan')->nullable();
            $table->string('periode'); // "Maret 2026"
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billings');
    }
};