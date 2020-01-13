<?php

namespace MediactiveDigital\MedKit\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

use Schema;

class CreateUsersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        Schema::create('users', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->string('name');
            $table->string('first_name');
            $table->string('email')->unique();
            $table->string('login')->unique();
            $table->string('password');
            $table->boolean('theme')->nullable()->default(0);
            $table->timestamp('last_activity')->useCurrent();

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {

        Schema::dropIfExists('users');
    }
}
