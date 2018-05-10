<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tickets', function (Blueprint $table) {
		    $table->increments('id');
            $table->integer('cid');
            $table->string('subject');
            $table->mediumText('body');
            $table->enum('status', ['New','In Progress', 'Closed']);
            $table->string("assigned_to");
            $table->enum('priority', ['Low','Normal','High']);
            $table->timestamps();
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
