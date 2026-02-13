<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_logs', function (Blueprint $table) {
            $table->id();
            $table->string('service'); // geocoder, routing, payment, sms
            $table->string('method');
            $table->text('url');
            $table->json('request_body')->nullable();
            $table->json('response_body')->nullable();
            $table->integer('status_code');
            $table->float('duration_ms');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_logs');
    }
};
