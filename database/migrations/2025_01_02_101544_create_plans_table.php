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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('base_plan_id');
            $table->string('name');
            $table->string('image');
            $table->string('price');
            $table->string('offer_price');
            $table->string('discount');
            $table->double('rating', 2,1);
            $table->integer('rating_count');
            $table->string('duration');
            $table->text('description');
            $table->string('recommendation')->nullable(); 
            $table->tinyInteger('is_recommended')->default(0);
            $table->string('services')->nullable();
            $table->tinyInteger('is_popular')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
