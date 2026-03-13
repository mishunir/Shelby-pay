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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

        $table->string('full_name');
        $table->string('email');
        $table->decimal('amount', 10, 2);

        $table->unsignedBigInteger('user_id');
        $table->unsignedBigInteger('order_id')->nullable();

        $table->string('invoice_id')->nullable();
        $table->string('status')->default('pending'); // pending, completed, failed

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
        Schema::dropIfExists('payments');
    }
};
