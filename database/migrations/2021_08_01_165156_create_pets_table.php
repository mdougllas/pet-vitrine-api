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

            $table->enum('age', ['Adult', 'Baby', 'Senior', 'Young']);
            $table->string('breed');
            $table->text('description');
            $table->string('name');
            $table->json('photo_urls');
            $table->string('petfinder_shelter_id');
            $table->bigInteger('petfinder_id')->unique();
            $table->enum('sex', ['Female', 'Male', 'Unknown']);
            $table->enum('species', ['Cat', 'Dog']);
            $table->enum('status', ['adoptable', 'adopted']);
            $table->foreignId('organization_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('url');

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
