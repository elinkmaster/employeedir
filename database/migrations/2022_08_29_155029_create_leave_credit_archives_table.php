<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaveCreditArchivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_credit_archives', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('employee_id');
            $table->double('credit');
            $table->tinyInteger('type')->default('1')->comment = "1 = Add Credit 2 = Add Past Credit 3 = Conversion 4 = LOA 5 = Use Credit";
            $table->tinyInteger('month')->default('0');
            $table->string('year', 5);
            $table->bigInteger('leave_id');
            $table->tinyInteger('status')->default('1');
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
        Schema::dropIfExists('leave_credit_archives');
    }
}
