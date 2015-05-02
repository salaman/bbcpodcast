<?php

use Illuminate\Database\Migrations\Migration;

class UpdateSchema extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('entries', function($table)
        {
            $table->string('service')->after('broadcast_at')->nullable();
            $table->integer('bitrate')->after('service')->nullable();
            $table->integer('size')->after('bitrate')->nullable();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('entries', function($table)
        {
            $table->dropColumn('service', 'bitrate', 'size');
        });
	}

}
