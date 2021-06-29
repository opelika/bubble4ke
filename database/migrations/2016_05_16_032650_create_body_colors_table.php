<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBodyColorsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('body_colors', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id');
			$table->integer('head_color');
			$table->integer('torso_color');
			$table->integer('left_arm_color');
			$table->integer('right_arm_color');
			$table->integer('left_leg_color');
			$table->integer('right_leg_color');
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
		Schema::drop('body_colors');
	}
}
