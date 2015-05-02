<?php

use Illuminate\Database\Migrations\Migration;

class DropExplicitColumn extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entries', function($table)
        {
            $table->dropColumn('explicit');
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
            $table->boolean('explicit')->default(0);
        });
    }

}
