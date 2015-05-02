<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUrlColumn extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('entries', function($table)
        {
            $table->string('url')->after('size')->nullable();
        });

        DB::update("UPDATE entries e
                    SET url = CONCAT('http://media.bbc.chrs.pw/', (SELECT programme_id FROM programmes p WHERE e.programme_id = p.id), '/', entry_id, '/', mediator_id, '.m4a')");
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
            $table->dropColumn('url');
        });
	}

}
