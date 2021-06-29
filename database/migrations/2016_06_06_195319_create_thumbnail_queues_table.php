<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThumbnailQueuesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('thumbnail_queues', function (Blueprint $table) {
			$table->increments('id');
			$table->string("specimen"); // Username or asset ID
			$table->string("type"); // 'asset' or 'user'
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
		Schema::drop('thumbnail_queues');
	}
}
