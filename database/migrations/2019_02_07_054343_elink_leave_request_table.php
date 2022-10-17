<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ElinkLeaveRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_request', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id');
            $table->dateTime('leave_date_from');
            $table->dateTime('leave_date_to');
            $table->decimal('number_of_days', 8, 2);
            $table->integer('leave_type_id');
            $table->integer('pay_type_id');
            $table->integer('attachment_id')->nullable(true);
            $table->dateTime('report_date');
            $table->string('contact_number');
            $table->string('reason');
            $table->integer('filed_by_id')->nullable(true);
            $table->integer('recommending_approval_by_id')->nullable(true);
            $table->dateTime('recommending_approval_by_signed_date')->nullable(true);
            $table->integer('recommending_approval_by_is_notified')->nullable(true)->default(0);
            $table->integer('approved_by_id')->nullable(true);
            $table->dateTime('approved_by_signed_date')->nullable(true);
            $table->integer('approved_by_is_notified')->nullable(true)->default(0);
            $table->integer('noted_by_id')->nullable(true);
            $table->dateTime('noted_by_signed_date')->nullable(true);
            $table->integer('noted_by_is_notified')->nullable(true)->default(0);
            $table->integer('approve_status_id')->nullable(true);
            $table->string('reason_for_disapproval')->nullable(true);
            $table->dateTime('date_filed');
            $table->dateTime('created_at')->nullable(true);
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
        Schema::dropIfExists('leave_request');
    }
}
