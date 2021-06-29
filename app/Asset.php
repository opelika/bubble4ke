<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
		'description',
		'price',
	];

	/**
	 * Get the user that owns the assets.
	 */
	public function getUser()
	{
		return $this->belongsTo('App\User');
	}
}
