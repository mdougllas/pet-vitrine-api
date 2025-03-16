<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('spider_jobs', function (Blueprint $table) {
            $table->json('number_of_pets')->after('number_of_shelters')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spider_jobs', function (Blueprint $table) {
            $table->dropColumn('number_of_pets');
        });
    }
};
