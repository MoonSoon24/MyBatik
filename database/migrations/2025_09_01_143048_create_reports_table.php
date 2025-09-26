<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->json('sales_data')->nullable();
            $table->json('promo_report_data')->nullable();
            $table->json('user_data')->nullable();
            $table->json('top_customers_filter_months')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reports');
    }
};