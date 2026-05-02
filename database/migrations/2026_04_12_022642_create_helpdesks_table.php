<?php
// database/migrations/2024_01_01_000003_create_helpdesks_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('helpdesks', function (Blueprint $table) {
            $table->id();
            $table->string('no_tiket')->unique();
            $table->foreignId('pelanggan_id')->constrained('pelanggans')->onDelete('cascade');
            $table->string('pelapor');
            $table->string('no_telepon_pelapor');
            $table->enum('kategori', [
                'gangguan_koneksi',
                'lambat',
                'putus_nyambung',
                'tidak_bisa_akses',
                'ganti_password_wifi',
                'relokasi',
                'upgrade_paket',
                'downgrade_paket',
                'pertanyaan_billing',
                'lainnya'
            ]);
            $table->enum('prioritas', ['rendah', 'sedang', 'tinggi', 'kritis'])->default('sedang');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->text('deskripsi');
            $table->text('solusi')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('helpdesks');
    }
};