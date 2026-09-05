<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE empresas ALTER COLUMN logo TYPE text');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE empresas ALTER COLUMN logo TYPE varchar(255)');
    }
};
