<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateHistoryTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        Schema::create('history', function(Blueprint $table) {

            $table->bigIncrements('id');

            // Which table are we tracking
            $table->string('reference_table');

            // Which record from the table are we referencing
            $table->unsignedBigInteger('reference_id');

            // Who made the action
            $table->unsignedBigInteger('actor_id');

            // What did they do
            $table->string('body');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {

        Schema::dropIfExists('history');
    }
}
