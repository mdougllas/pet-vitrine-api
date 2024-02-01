<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->bigInteger('ad_id')->unique();
            $table->string('ad_set_id');
            $table->float('amount_spent', 8, 2)->nullable();
            $table->float('budget', 8, 2);
            $table->string('campaign_id');
            $table->float('cost_per_result', 8, 2)->nullable();
            $table->string('creative_id');
            $table->timestamp('end_time');
            $table->integer('impressions')->nullable();
            $table->string('payment_id')->unique()->nullable();
            $table
                ->foreignId('pet_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->integer('results')->nullable();
            $table->integer('reach')->nullable();
            $table->timestamp('start_time');
            $table->timestamps();
            $table
                ->foreignId('user_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ads');
    }
}
