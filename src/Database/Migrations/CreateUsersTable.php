<?php
namespace MediactiveDigital\MedKit\Database\Migrations;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        Schema::create('users', function (Blueprint $table) {

            $table->increments('id');
            $table->string('name');
            $table->string('first_name');
            $table->string('email')->unique();
            $table->string('login')->unique();
            $table->string('password');
            $table->boolean('theme')->nullable()->default(0);
            $table->timestamp('last_activity')->default(DB::raw("NOW()"));

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->unsignedInteger('created_by')->nullable()->default(null);
            $table->unsignedInteger('updated_by')->nullable()->default(null);
            $table->unsignedInteger('deleted_by')->nullable()->default(null);
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
