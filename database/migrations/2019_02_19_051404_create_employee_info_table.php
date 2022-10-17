<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_info', function (Blueprint $table) {
            $table->increments('id');
            $table->string('eid', 50);
            $table->string('first_name', 50);
            $table->string('middle_name', 50)->nullable(true);
            $table->string('last_name', 50);
            $table->string('email', 80);
            $table->string('email2', 80)->nullable(true);
            $table->string('email3', 80)->nullable(true);
            $table->string('alias', 80)->nullable(true);
            $table->string('team_name', 80)->nullable(true);
            $table->string('dept_code', 20)->nullable(true);
            $table->string('position_name', 50);
            $table->integer('supervisor_id')->nullable(true);
            $table->string('role_id', 50)->nullable(true);
            $table->string('supervisor_name', 150)->nullable(true);
            $table->string('manager_name', 150)->nullable(true);
            $table->dateTime('birth_date')->nullable(true);
            $table->dateTime('hired_date')->nullable(true);
            $table->dateTime('prod_date')->nullable(true);
            $table->string('password', 200);
            $table->integer('usertype')->default(1);
            $table->integer('gender')->nullable(true)->default(0);
            $table->integer('manager_id')->nullable(true)->default(0);
            $table->integer('division_id')->default(0);
            $table->string('division_name', 100)->nullable(true);
            $table->integer('account_id')->default(0);
            $table->integer('status')->default(1);
            $table->string('ext', 20)->nullable(true);
            $table->string('wave', 20)->nullable(true);
            $table->integer('all_access')->nullable(true)->default(0); // not used
            $table->string('profile_img', 200)->nullable(true)->default('http://dir.elink.corp/public/img/nobody_m.original.jpg');
            $table->string('sss', 200)->nullable(true);
            $table->string('pagibig', 200)->nullable(true);
            $table->string('philhealth', 200)->nullable(true);
            $table->string('tin', 200)->nullable(true);
            $table->string('address', 200)->nullable(true);
            $table->decimal('leave_credit', 5, 2)->nullable(true)->default(0);
            $table->integer('is_admin')->nullable(true)->default(0);
            $table->integer('is_erp')->nullable(true)->default(0);
            $table->integer('is_hr')->nullable(true)->default(0);
            $table->integer('is_ra')->nullable(true)->default(0);
            $table->string('remember_token', 200)->nullable(true);
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
        Schema::dropIfExists('employee_info');
    }
}
