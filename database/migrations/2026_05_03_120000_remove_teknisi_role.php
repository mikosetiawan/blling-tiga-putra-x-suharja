<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Hapus role teknisi dari database yang sudah pernah di-seed (Spatie Permission).
     */
    public function up(): void
    {
        $ids = DB::table('roles')
            ->where('name', 'teknisi')
            ->where('guard_name', 'web')
            ->pluck('id');
        if ($ids->isEmpty()) {
            return;
        }
        DB::table('role_has_permissions')->whereIn('role_id', $ids)->delete();
        DB::table('model_has_roles')->whereIn('role_id', $ids)->delete();
        DB::table('roles')->whereIn('id', $ids)->delete();
    }

    public function down(): void
    {
        // sengaja kosong: role teknisi tidak dipulihkan
    }
};
