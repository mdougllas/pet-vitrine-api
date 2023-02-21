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
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->enum('age', ['Adult', 'Baby', 'Senior', 'Young']);
            $table->string('breed');
            $table->text('description');
            $table->string('name');
            $table->json('photo_urls');
            $table->enum('sex', ['Female', 'Male', 'Unknown']);
            $table->enum('species', ['Cat', 'Dog']);
            $table->enum('status', ['adoptable', 'adopted']);
            $table->foreignId('organization_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('petfinder_shelter_id');
            $table->bigInteger('petfinder_id');

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
