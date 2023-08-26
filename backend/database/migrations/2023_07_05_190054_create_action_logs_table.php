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
        Schema::create('action_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('object_id')->unsigned();
            $table->bigInteger('owner_id')->unsigned();
            $table->string('action', 20)->nullable();
            $table->string('info')->nullable();
            $table->json('before')->nullable();
            $table->json('before_changes')->nullable();
            $table->json('after')->nullable();
            $table->json('after_changes')->nullable();
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
        Schema::dropIfExists('action_logs');
    }
};
