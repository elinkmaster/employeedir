<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateElinkReferralTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('elink_referral', function (Blueprint $table) {
            $table->increments('id');
            $table->string('referrer_first_name', 200)->nullable(true);
            $table->string('referrer_middle_name', 200)->nullable(true);
            $table->string('referrer_last_name', 200)->nullable(true);
            $table->string('referrer_department', 200)->nullable(true);
            $table->string('referral_first_name', 200)->nullable(true);
            $table->string('referral_middle_name', 200)->nullable(true);
            $table->string('referral_last_name', 200)->nullable(true);
            $table->string('referral_contact_number', 200)->nullable(true);
            $table->string('referral_email', 200)->nullable(true);
            $table->string('position_applied', 200)->nullable(true);
            $table->dateTime('created_at')->nullable(true)->default(DB::raw('CURRENT_TIMESTAMP'));
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
        Schema::dropIfExists('elink_referral');
    }
}
