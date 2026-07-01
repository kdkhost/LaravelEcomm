<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');

            Schema::table('payments', function (Blueprint $table) {
                $table->string('payment_method', 50)->default('cod')->change();
            });

            DB::statement('PRAGMA foreign_keys = ON');
        } else {
            DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method ENUM('cod', 'paypal', 'stripe', 'bank_transfer', 'other', 'mercadopago') DEFAULT 'cod'");
        }
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
            Schema::table('payments', function (Blueprint $table) {
                $table->string('payment_method', 50)->default('cod')->change();
            });
            DB::statement('PRAGMA foreign_keys = ON');
        } else {
            DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method ENUM('cod', 'paypal', 'stripe', 'bank_transfer', 'other') DEFAULT 'cod'");
        }
    }
};
