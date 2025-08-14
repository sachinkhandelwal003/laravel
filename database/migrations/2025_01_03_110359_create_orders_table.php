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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->bigInteger('plan_id');
            $table->bigInteger('vehicle_id');
            $table->longText('address_json')->nullable();
            $table->date('service_date')->nullable();
            $table->time('service_time')->nullable();
            $table->string('transaction_id')->nullable();
            $table->tinyInteger('payment_type')->default(0);
            $table->tinyInteger('payment_status')->default(0);
            $table->longText('payment_json')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0=pending, 1=completed, 2=cancelled');
            $table->text('coustomer_comments')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
