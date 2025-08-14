<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('active_deals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('offer_type');
            $table->string('valid_date');
            $table->string('discount');
            $table->string('price');
            $table->text('code')->nullable();
            $table->text('description')->nullable();
            $table->integer('status')->default('1');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('active_deals');
    }
};
