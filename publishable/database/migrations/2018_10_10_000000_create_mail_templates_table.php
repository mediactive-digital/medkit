<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMailTemplatesTable extends Migration {

    public function up() {

        Schema::create('mail_templates', function(Blueprint $table) {

            $table->bigIncrements('id');
            $table->string('mailable');
            $table->json('subject')->nullable();
            $table->json('html_template');
            $table->json('text_template')->nullable();
            
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

        Schema::dropIfExists('mail_templates');
    }
}
