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
            $table->uuid('uuid');
            $table->char('campaign_id', 13);
            $table->char('ad_set_id', 13);
            $table->char('ad_id', 13);
            $table->char('creative_id', 13);
            $table->float('budget', 8, 2);
            $table->integer('results');
            $table->integer('reach');
            $table->integer('impressions');
            $table->float('cost_per_result', 8, 2);
            $table->float('amount_spent', 8, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
            $table
                ->foreignId('user_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
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
