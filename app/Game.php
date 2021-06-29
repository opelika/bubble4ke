<?php

namespace App;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
	public function getOnlineStatus()
	{
		if(strtotime($this->last_beat) > Carbon::now()->timestamp - (60 * 1.5)){
			return true;
		} else {
			return false;
		}
	}
}
