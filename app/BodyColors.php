<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BodyColors extends Model
{
	/**
	 * Get the user that owns the body colors.
	 */
	public function getUser()
	{
		return $this->belongsTo('App\User');
	}
}
