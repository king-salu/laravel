<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TpzMemories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('tpz_memories', function (Blueprint $table) {
            $table->increments('sn');
            $table->integer('ownerid');
            $table->integer('status');
            $table->timestamp('entrydate')->useCurrent =true;
            $table->dateTime('duedate');
            $table->text('caption');
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
        Schema::drop('tpz_memories');
    }
}
