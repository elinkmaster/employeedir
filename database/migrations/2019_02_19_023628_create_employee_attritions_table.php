<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeAttritionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_attritions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('employee_id', 100);
            $table->string('employee_name', 100)->nullable(true);
            $table->dateTime('start_work_date')->nullable(true);
            $table->dateTime('last_work_date')->nullable(true);
            $table->text('employee_type')->nullable(true);
            $table->string('particulars')->nullable(true);
            $table->string('alias', 100)->nullable(true);
            $table->string('it_status', 20)->nullable(true);
            $table->string('ra_status', 20)->nullable(true);
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('updated_at')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_attritions');
    }
}
