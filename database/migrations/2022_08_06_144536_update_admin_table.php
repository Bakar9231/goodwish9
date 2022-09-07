<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAdminTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_admin', function (Blueprint $table) {
            $table->string('name')
                    ->after('id')
                    ->nullable();

            $table->string('mobile')
                    ->after('password')
                    ->nullable();

            $table->string('role')
                    ->after('mobile')
                    ->nullable();
            
            $table->string('referral_code')
                    ->after('role')
                    ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
