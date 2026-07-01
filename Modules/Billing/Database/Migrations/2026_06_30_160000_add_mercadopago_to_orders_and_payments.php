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
        Schema::table('orders', function (Blueprint $table): void {
            $table->string('mercadopago_preference_id')->nullable()->after('transaction_reference');
            $table->string('mercadopago_payment_id')->nullable()->after('mercadopago_preference_id');
            $table->string('mercadopago_status')->nullable()->after('mercadopago_payment_id');
            $table->json('mercadopago_payload')->nullable()->after('mercadopago_status');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY payment_method ENUM('cod', 'paypal', 'stripe', 'mercadopago') DEFAULT 'cod'");
            DB::statement("ALTER TABLE payments MODIFY payment_method ENUM('cod', 'paypal', 'stripe', 'mercadopago', 'bank_transfer', 'other') DEFAULT 'cod'");
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn([
                'mercadopago_preference_id',
                'mercadopago_payment_id',
                'mercadopago_status',
                'mercadopago_payload',
            ]);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY payment_method ENUM('cod', 'paypal', 'stripe') DEFAULT 'cod'");
            DB::statement("ALTER TABLE payments MODIFY payment_method ENUM('cod', 'paypal', 'stripe', 'bank_transfer', 'other') DEFAULT 'cod'");
        }
    }
};
