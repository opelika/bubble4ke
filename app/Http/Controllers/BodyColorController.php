<?php

namespace App\Http\Controllers;

use App\BodyColors;

use Illuminate\Http\Request;

use App\User;

use Auth;

use App\Http\Requests;

class BodyColorController extends Controller
{
	public $colornames = [ "White", "Brick yellow", "Light reddish violet", "Pastel Blue", "Nougat", "Bright red", "Bright blue", "Bright yellow", "Black", "Dark green", "Medium green", "Bright green", "Dark orange", "Light blue", "Medium red", "Medium blue", "Bright violet", "Br. yellowish orange", "Bright orange", "Bright bluish green", "Br. yellowish green", "Light orange", "Sand blue", "Earth green", "Sand green", "Sand red", "Reddish brown", "Medium stone grey", "Dark stone grey", "Light stone grey", "Brown", "Cool yellow", "Institutional white", ];
	public $colorvals = [ "1", "5", "9", "11", "18", "21", "23", "24", "26", "28", "29", "37", "38", "45", "101", "102", "104", "105", "106", "107", "119", "125", "135", "141", "151", "153", "192", "194", "199", "208", "217", "226", "1001", ];
	public $colorrgbvals = [ "242,243,243", "215,197,154", "232,186,200", "128,187,219", "204,142,105", "196,40,28", "13,105,172", "245,208,48", "27,42,53", "40,127,71", "161,196,140", "75,151,75", "160,95,53", "180,210,228", "218,134,122", "110,153,202", "107,50,124", "226,155,64", "218,133,65", "0,143,156", "164,189,71", "234,184,146", "116,134,157", "39,70,45", "120,144,130", "149,121,119", "105,64,40", "163,162,165", "99,95,98", "229,228,223", "124,92,70", "253,234,141", "248,248,248", ];

	public function getBodyColorsFromId ($user) {
		$user = User::where('id', $user)->first();
		$colors = null;

		if($user) {
			$colors = $user->getColors()->first();
		}

		if($colors) {
			return view('gameclient.data.colors', compact('colors'));
		} else {
			$colors = new BodyColors;
			$colors->head_color = 24;
			$colors->left_arm_color = 24;
			$colors->left_leg_color = 119;
			$colors->right_arm_color = 24;
			$colors->right_leg_color = 119;
			$colors->torso_color = 23;
			
			return view('gameclient.data.colors', compact('colors'));
		}
	}

	public function getBodyColorsFromName ($user) {
		$user = User::where('name', $user)->first();
		$colors = null;

		if($user) {
			$colors = $user->getColors()->first();
		}

		if($colors) {
			return view('gameclient.data.colors', compact('colors'));
		} else {
			$colors = new BodyColors;
			$colors->head_color = 24;
			$colors->left_arm_color = 24;
			$colors->left_leg_color = 119;
			$colors->right_arm_color = 24;
			$colors->right_leg_color = 119;
			$colors->torso_color = 23;
			
			return view('gameclient.data.colors', compact('colors'));
		}
	}

	public function changeBodyColors(Request $request) {
		$colors = null;

		if(!$request->part) {
			return "No part specified";
		}

		if(!$request->color) {
			return "No color specified";
		}

		if(!in_array($request->color, $this->colorvals)) {
			return "Invalid color specified";
		}

		if(Auth::user()->getColors()->first()) {
			$colors = BodyColors::where('user_id', Auth::user()->id)->first();

			switch($request->part) {
				default:
					return "Invalid part specified";
				case "head":
					$colors->head_color = $request->color;
					break;
				case "torso":
					$colors->torso_color = $request->color;
					break;
				case "larm":
					$colors->left_arm_color = $request->color;
					break;
				case "lleg":
					$colors->left_leg_color = $request->color;
					break;
				case "rarm":
					$colors->right_arm_color = $request->color;
					break;
				case "rleg":
					$colors->right_leg_color = $request->color;
					break;
			}

			$colors->save();
		} else {
			return "Invalid request";
		}

		if($colors) {
			return $this->colorrgbvals[array_search($request->color, $this->colorvals)];
		}
		
		return "Unknown error";
	}

	public function showColorsUI() {
		$colors = null;

		if(Auth::user()->getColors()->first()) {
			$colors = BodyColors::where('user_id', Auth::user()->id)->first();
		} else {
			// Defaults
			
			$colors = new BodyColors;
			$colors->user_id = Auth::user()->id;
			$colors->head_color = 24;
			$colors->torso_color = 23;
			$colors->left_arm_color = 24;
			$colors->right_arm_color = 24;
			$colors->left_leg_color = 119;
			$colors->right_leg_color = 119;
			$colors->save();
		}

		return view('character.colors', ['names' => $this->colornames, 'rgbvals' => $this->colorrgbvals, 'codes' => $this->colorvals, 'colors' => $colors]);
	}
}
