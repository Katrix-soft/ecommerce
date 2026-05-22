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
        Schema::table('users', function (Blueprint $table) {
            // Limits & Quotas
            $table->integer('max_products')->default(50)->after('store_currency');
            $table->integer('max_users')->default(5)->after('max_products');
            $table->integer('max_orders_per_month')->default(100)->after('max_users');

            // Scheduled Maintenance
            $table->dateTime('maintenance_starts_at')->nullable()->after('max_orders_per_month');
            $table->dateTime('maintenance_ends_at')->nullable()->after('maintenance_starts_at');

            // Billing details
            $table->decimal('billing_plan_price', 10, 2)->default(0.00)->after('maintenance_ends_at');
            $table->date('billing_next_due_date')->nullable()->after('billing_plan_price');
            $table->string('billing_cycle')->default('monthly')->after('billing_next_due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'max_products',
                'max_users',
                'max_orders_per_month',
                'maintenance_starts_at',
                'maintenance_ends_at',
                'billing_plan_price',
                'billing_next_due_date',
                'billing_cycle',
            ]);
        });
    }
};
