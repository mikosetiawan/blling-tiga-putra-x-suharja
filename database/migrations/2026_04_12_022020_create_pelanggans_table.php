<?php
// database/migrations/2024_01_01_000001_create_pelanggans_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelanggans', function (Blueprint $table) {
            $table->id();
            $table->string('id_pelanggan')->unique(); // TPP2024-001
            $table->string('nama_lengkap');
            $table->string('nama_perusahaan')->nullable();
            $table->string('no_ktp_nib')->nullable();
            $table->string('npwp')->nullable();
            $table->string('no_telepon');
            $table->string('no_whatsapp')->nullable();
            $table->string('email')->nullable();
            // Alamat
            $table->text('alamat_lengkap');
            $table->string('rt')->nullable();
            $table->string('rw')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kota')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kode_pos')->nullable();
            $table->string('patokan')->nullable();
            // Paket
            $table->enum('paket', ['10mbps', '20mbps', '30mbps', '50mbps', '100mbps', 'dedicated']);
            $table->enum('jenis_koneksi', ['fiber_optik', 'wireless', 'lainnya']);
            $table->string('kode_paket')->nullable();
            $table->decimal('harga_paket', 15, 2)->default(0);
            $table->date('tgl_mulai')->nullable();
            $table->date('tgl_jatuh_tempo')->nullable();
            $table->text('catatan_paket')->nullable();
            // Status
            $table->enum('status', ['aktif', 'nonaktif', 'suspend'])->default('aktif');
            $table->date('tanggal_daftar');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelanggans');
    }
};