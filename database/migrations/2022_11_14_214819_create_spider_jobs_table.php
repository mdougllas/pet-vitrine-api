<?php

use App\Models\SpiderJob;
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
        Schema::create('spider_jobs', function (Blueprint $table) {
            $table->boolean('job_running')->default(false);
            $table->bigInteger('last_page_processed');
            $table->bigInteger('number_of_shelters');
            $table->timestamps();
        });

        $spider = new SpiderJob;
        $spider->job_running = 0;
        $spider->last_page_processed = 0;
        $spider->number_of_shelters = 0;

        $spider->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spider_jobs');
    }
};
