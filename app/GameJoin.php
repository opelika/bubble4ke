<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GameJoin extends Model
{
	/**
	 * Get the user that owns the join tokens.
	 */
	public function getUser()
	{
		return $this->belongsTo('App\User');
	}
}
