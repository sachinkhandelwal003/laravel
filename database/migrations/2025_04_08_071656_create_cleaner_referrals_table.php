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
        Schema::create('cleaner_referrals', function (Blueprint $table) {
            $table->id();
            $table->string('referral_code')->unique();
            $table->unsignedBigInteger('cleaner_id')->nullable();
            $table->integer('reward')->default(0);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cleaner_referrals');
    }
};
