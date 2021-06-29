<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('games', function (Blueprint $table) {
			$table->increments('id'); // Game ID
			$table->integer('author')->required(); // Creator ID
			$table->text('name')->required(); // Name of game
			$table->text('description')->required(); // Description of game
			$table->string('ip')->required(); // IP of place
			$table->string('port')->required(); // Port of place
			$table->string('api_key')->required(); // API key for heartbeat
			$table->timestamp('last_beat'); // Last heartbeat
			$table->integer('playing'); // Amount of players currently playing
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
		Schema::drop('games');
	}
}
