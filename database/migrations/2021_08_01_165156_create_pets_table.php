<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');

            $table
                ->foreignId('ad_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('breed');
            $table->text('description');
            $table->string('name');
            $table->tinyInteger('last_page_processed');
            $table->json('photo_urls');
            $table->enum('sex', ['female', 'male']);
            $table->enum('species', ['Cat', 'Dog']);
            $table->enum('status', ['adoptable', 'adopted']);
            $table->bigInteger('organization_id');
            $table->bigInteger('petfinder_id');
            $table->enum('age', ['adult', 'baby', 'senior', 'young']);

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
        Schema::dropIfExists('pets');
    }
}
