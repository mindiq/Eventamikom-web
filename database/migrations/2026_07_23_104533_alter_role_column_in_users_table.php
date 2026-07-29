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
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'pgsql') {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE users ALTER COLUMN role TYPE VARCHAR(50)");
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE users ALTER COLUMN role SET DEFAULT 'user'");
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE users ALTER COLUMN role SET NOT NULL");
        } else {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) NOT NULL DEFAULT 'user'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'pgsql') {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE users ALTER COLUMN role TYPE VARCHAR(50)");
        } else {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'user') NOT NULL DEFAULT 'user'");
        }
    }
};
