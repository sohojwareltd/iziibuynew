<?php

use App\Console\Commands\ImportLegacyIziibuyDatabaseCommand;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Columns that are nullable on the legacy MySQL schema (`legacy_iziibuy` / LEGACY_DB_*)
 * but were non-nullable on the target app schema are aligned here so legacy imports
 * (see {@see ImportLegacyIziibuyDatabaseCommand}) do not fail on NULL values.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('category_product', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['product_id']);
        });

        Schema::table('category_product', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->change();
            $table->unsignedBigInteger('product_id')->nullable()->change();
        });

        Schema::table('category_product', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('categories')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });

        Schema::table('charges', function (Blueprint $table) {
            $table->enum('payment_type', ['Unresolved', 'Test', 'Real'])->nullable()->change();
        });

        Schema::table('enterprise_onboardings', function (Blueprint $table) {
            $table->string('company_name')->nullable()->change();
            $table->string('company_email')->nullable()->change();
            $table->string('company_address')->nullable()->change();
            $table->string('company_registration')->nullable()->change();
            $table->string('company_domain')->nullable()->change();
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->bigInteger('price')->default(0)->nullable()->change();
        });

        Schema::table('payment_method_accesses', function (Blueprint $table) {
            $table->string('company_name')->nullable()->change();
            $table->string('company_email')->nullable()->change();
            $table->string('company_address')->nullable()->change();
            $table->string('company_registration')->nullable()->change();
            $table->string('company_domain')->nullable()->change();
            $table->string('contract_url')->nullable()->change();
        });

        Schema::table('retailer_earnings', function (Blueprint $table) {
            $table->dropForeign(['shop_id']);
        });

        Schema::table('retailer_earnings', function (Blueprint $table) {
            $table->unsignedBigInteger('shop_id')->nullable()->change();
        });

        Schema::table('retailer_earnings', function (Blueprint $table) {
            $table->foreign('shop_id')->references('id')->on('shops')->cascadeOnDelete();
        });

        Schema::table('services', function (Blueprint $table) {
            $table->integer('free_from')->nullable()->change();
            $table->integer('free_to')->nullable()->change();
        });

        Schema::table('shops', function (Blueprint $table) {
            $table->string('subscription_id')->nullable()->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('service_type')->default('both')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('service_type')->default('both')->nullable(false)->change();
        });

        Schema::table('shops', function (Blueprint $table) {
            $table->string('subscription_id')->nullable(false)->change();
        });

        Schema::table('services', function (Blueprint $table) {
            $table->integer('free_from')->nullable(false)->change();
            $table->integer('free_to')->nullable(false)->change();
        });

        Schema::table('retailer_earnings', function (Blueprint $table) {
            $table->dropForeign(['shop_id']);
        });

        Schema::table('retailer_earnings', function (Blueprint $table) {
            $table->unsignedBigInteger('shop_id')->nullable(false)->change();
        });

        Schema::table('retailer_earnings', function (Blueprint $table) {
            $table->foreign('shop_id')->references('id')->on('shops')->cascadeOnDelete();
        });

        Schema::table('payment_method_accesses', function (Blueprint $table) {
            $table->string('company_name')->nullable(false)->change();
            $table->string('company_email')->nullable(false)->change();
            $table->string('company_address')->nullable(false)->change();
            $table->string('company_registration')->nullable(false)->change();
            $table->string('company_domain')->nullable(false)->change();
            $table->string('contract_url')->default('0')->nullable(false)->change();
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->bigInteger('price')->default(0)->nullable(false)->change();
        });

        Schema::table('enterprise_onboardings', function (Blueprint $table) {
            $table->string('company_name')->nullable(false)->change();
            $table->string('company_email')->nullable(false)->change();
            $table->string('company_address')->nullable(false)->change();
            $table->string('company_registration')->nullable(false)->change();
            $table->string('company_domain')->nullable(false)->change();
        });

        Schema::table('charges', function (Blueprint $table) {
            $table->enum('payment_type', ['Unresolved', 'Test', 'Real'])->nullable(false)->change();
        });

        Schema::table('category_product', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['product_id']);
        });

        Schema::table('category_product', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable(false)->change();
            $table->unsignedBigInteger('product_id')->nullable(false)->change();
        });

        Schema::table('category_product', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('categories')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });
    }
};
