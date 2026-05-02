<?php

namespace Database\Seeders;

use App\Models\Billing;
use App\Models\Helpdesk;
use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $teknisiRole = Role::firstOrCreate(['name' => 'teknisi']);
        $pelangganRole = Role::firstOrCreate(['name' => 'pelanggan']);

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@3pp.co.id'],
            [
                'name' => 'Admin 3PP',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole($adminRole);

        $teknisi = User::firstOrCreate(
            ['email' => 'teknisi@3pp.co.id'],
            [
                'name' => 'Teknisi Satu',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $teknisi->assignRole($teknisiRole);

        // Pelanggan sesuai Laporan Data Pelanggan PDF
        $pelanggans = [
            ['TPP2024-001', 'PT. Krakatau Bandar Samudra', 'dedicated', 15000000, 'aktif'],
            ['TPP2024-002', 'PT. Intermatra', 'dedicated', 12000000, 'aktif'],
            ['TPP2024-003', 'PT. KTI', 'dedicated', 12000000, 'aktif'],
            ['TPP2024-004', 'PT. KBI', 'dedicated', 10000000, 'aktif'],
            ['TPP2024-005', 'PT. Osaka Steel', 'dedicated', 10000000, 'aktif'],
            ['TPP2024-006', 'PT. KPMS Indonesia', 'dedicated', 8000000, 'aktif'],
            ['TPP2024-007', 'PT. Honda Banten', '100mbps', 3500000, 'aktif'],
            ['TPP2024-008', 'PT. Samudera Bahana', '100mbps', 3500000, 'aktif'],
        ];

        $createdPelanggans = [];

        foreach ($pelanggans as [$id, $nama, $paket, $harga, $status]) {
            $p = Pelanggan::firstOrCreate(
                ['id_pelanggan' => $id],
                [
                    'nama_lengkap' => 'Perwakilan ' . $nama,
                    'nama_perusahaan' => $nama,
                    'no_telepon' => '0254-' . rand(300000, 999999),
                    'email' => strtolower(str_replace([' ', '.'], ['', ''], $nama)) . '@example.com',
                    'alamat_lengkap' => 'Kawasan Industri Krakatau, Cilegon, Banten',
                    'kelurahan' => 'Warnasari',
                    'kecamatan' => 'Citangkil',
                    'kota' => 'Cilegon',
                    'provinsi' => 'Banten',
                    'kode_pos' => '42443',
                    'paket' => $paket,
                    'jenis_koneksi' => 'fiber_optik',
                    'kode_paket' => strtoupper($paket) . '-001',
                    'harga_paket' => $harga,
                    'tgl_mulai' => now()->subYears(1),
                    'tgl_jatuh_tempo' => now()->setDay(1),
                    'status' => $status,
                    'tanggal_daftar' => now()->subYears(1),
                ]
            );

            // Buat user untuk pelanggan agar bisa login
            $userPelanggan = User::firstOrCreate(
                ['email' => $p->email],
                [
                    'name' => $p->nama_lengkap,
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
            $userPelanggan->assignRole($pelangganRole);

            $createdPelanggans[] = $p;

            // Buat billing Maret 2026 (sudah lunas)
            Billing::firstOrCreate(
                ['no_invoice' => 'INV/2026/03/' . str_pad($p->id, 4, '0', STR_PAD_LEFT)],
                [
                    'pelanggan_id' => $p->id,
                    'tanggal_invoice' => '2026-03-01',
                    'jatuh_tempo' => '2026-03-10',
                    'tanggal_bayar' => '2026-03-05',
                    'jumlah' => $harga,
                    'denda' => 0,
                    'total_bayar' => $harga,
                    'status_bayar' => 'lunas',
                    'metode_bayar' => 'Transfer Bank',
                    'keterangan' => 'Tagihan Internet Maret 2026',
                    'periode' => 'Maret 2026',
                    'verified_by' => $admin->id,
                    'verified_at' => '2026-03-05',
                ]
            );

            // Buat billing April 2026 (belum bayar untuk beberapa)
            $statusBayar = in_array($id, ['TPP2024-007', 'TPP2024-008']) ? 'belum_bayar' : 'lunas';
            Billing::firstOrCreate(
                ['no_invoice' => 'INV/2026/04/' . str_pad($p->id, 4, '0', STR_PAD_LEFT)],
                [
                    'pelanggan_id' => $p->id,
                    'tanggal_invoice' => '2026-04-01',
                    'jatuh_tempo' => '2026-04-10',
                    'tanggal_bayar' => $statusBayar === 'lunas' ? '2026-04-05' : null,
                    'jumlah' => $harga,
                    'denda' => 0,
                    'total_bayar' => $harga,
                    'status_bayar' => $statusBayar,
                    'metode_bayar' => $statusBayar === 'lunas' ? 'Transfer Bank' : null,
                    'keterangan' => 'Tagihan Internet April 2026',
                    'periode' => 'April 2026',
                    'verified_by' => $statusBayar === 'lunas' ? $admin->id : null,
                    'verified_at' => $statusBayar === 'lunas' ? '2026-04-05' : null,
                ]
            );
        }

        // Contoh tiket helpdesk
        $tickets = [
            [$createdPelanggans[6], 'gangguan_koneksi', 'tinggi', 'open', 'Koneksi internet terputus sejak pagi, sudah restart router tidak ada perubahan'],
            [$createdPelanggans[7], 'lambat', 'sedang', 'in_progress', 'Kecepatan internet sangat lambat saat jam kerja, padahal paket 100 Mbps'],
            [$createdPelanggans[0], 'pertanyaan_billing', 'rendah', 'resolved', 'Pertanyaan mengenai tagihan bulan Maret 2026'],
            [$createdPelanggans[1], 'upgrade_paket', 'sedang', 'open', 'Ingin upgrade paket ke dedicated yang lebih tinggi'],
        ];

        foreach ($tickets as $i => [$pelanggan, $kategori, $prioritas, $status, $deskripsi]) {
            Helpdesk::firstOrCreate(
                ['no_tiket' => 'TKT/2026/04/' . str_pad($i + 1, 4, '0', STR_PAD_LEFT)],
                [
                    'pelanggan_id' => $pelanggan->id,
                    'pelapor' => $pelanggan->nama_lengkap,
                    'no_telepon_pelapor' => $pelanggan->no_telepon,
                    'kategori' => $kategori,
                    'prioritas' => $prioritas,
                    'status' => $status,
                    'deskripsi' => $deskripsi,
                    'solusi' => $status === 'resolved' ? 'Sudah dijelaskan melalui telepon, masalah terselesaikan.' : null,
                    'assigned_to' => $teknisi->id,
                    'resolved_at' => $status === 'resolved' ? now() : null,
                ]
            );
        }
    }
}