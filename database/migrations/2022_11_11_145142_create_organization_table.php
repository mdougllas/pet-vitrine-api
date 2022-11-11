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
        Schema::create('organization', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('address_1');
            $table->string('address_2');
            $table->string('city');
            $table->tinyInteger('latitude');
            $table->tinyInteger('longitude');
            $table->string('name');
            $table->smallInteger('postal_code');
            $table->string('petfinder_id');
            $table->tinyText('state', 30);

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
        Schema::dropIfExists('organization');
    }
};
