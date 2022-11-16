<?php

use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('address_1')->nullable();
            $table->string('address_2')->nullable();
            $table->string('city');
            $table->string('country');
            $table->tinyInteger('latitude');
            $table->tinyInteger('longitude');
            $table->string('name');
            $table->integer('postal_code');
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
        Schema::dropIfExists('organizations');
    }
};
