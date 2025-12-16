<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('event_ticket', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('customer_id')->default(0);

            // Enum status
            $table->enum('status', ['open', 'hold', 'closed'])->default('open');

            // Unique hash key
            $table->string('hash_key')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_ticket');
    }
};
