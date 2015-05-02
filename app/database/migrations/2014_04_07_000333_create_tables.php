<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('programmes', function(Blueprint $table) {
			$table->increments('id');
            $table->string('programme_id', 8);
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image', 2083)->nullable();
			$table->timestamps();
		});

        Schema::create('entries', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('programme_id')->unsigned();
            $table->string('entry_id', 8);
            $table->string('mediator_id', 8);
            $table->string('title');
            $table->text('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->integer('duration');
            $table->tinyInteger('status')->default(0);
            $table->string('image', 2083)->nullable();
            $table->boolean('explicit')->default(0);
            $table->timestamp('broadcast_at');
            $table->timestamps();

            $table->foreign('programme_id')->references('id')->on('programmes');
        });
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('entries');
        Schema::drop('programmes');
	}

}
