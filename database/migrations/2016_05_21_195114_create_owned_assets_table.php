<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOwnedAssetsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('owned_assets', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id');
			$table->integer('asset_id');
			$table->boolean('wearing');
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
		Schema::drop('owned_assets');
	}
}
