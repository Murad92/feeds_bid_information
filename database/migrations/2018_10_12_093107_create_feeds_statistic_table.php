<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedsStatisticTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feeds_statistic', function (Blueprint $table) {
            $table->string('feed_name')->default('')->index();
            $table->float('bid', 12, 9)->default(0);
            $table->date('date')->default(date('Y-m-d'))->index();
            $table->string('country')->default('XX')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feeds_statistic');
    }
}
