<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateElinkActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('elink_activities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 100)->nullable(true);
            $table->string('subtitle', 100)->nullable(true);
            $table->text('message')->nullable(true);
            $table->string('image_url', 200)->nullable(true);
            $table->dateTime('activity_date')->nullable(true);
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('updated_at')->nullable(true);
            $table->dateTime('deleted_at')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('elink_activities');
    }
}
