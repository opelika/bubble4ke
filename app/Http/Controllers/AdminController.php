<?php

namespace App\Http\Controllers;

use App\IpBans;

use App\User;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Asset;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
	public $limited = [
		"EnergyCell",
		"succ",
	];

	public function preventLimited()
	{
		if(in_array(Auth::user()->name, $this->limited))
		{
			return abort(404);
		}
	}

	// /admin
	public function showHome()
	{
		return view('admin.home', ['limited' => $this->limited]);
	}

	// /admin/ipbans
	public function showIpBans()
	{
		$this->preventLimited();

		$ipbans = IpBans::all();
		return view('admin.ipbans', ['ipbans' => $ipbans]);
	}
	
	public function newIpBan()
	{
		$this->preventLimited();

		return view('admin.newipban');
	}
	
	public function addNewIpBan(Request $request)
	{
		$this->preventLimited();

		$this->validate($request, [
			'ip' => 'required|ip',
		], [
			'ip.ip' => 'Enter a valid IP.',
			'ip.required' => 'Enter an IP.'
		]);
		
		$ipban = IpBans::create(['ip' => $request->ip]);
		$ipban->save();
		
		return redirect('/admin/ipbans');
	}
	
	public function deleteIpBan(Request $request)
	{
		$this->preventLimited();

		$ipban = IpBans::where('id', $request->id)->first();
		if ($ipban) {
			\Session::flash('flash_message', 'Deleted IP ban for ' . $ipban->ip . '.');
			$ipban->delete();
		} else {
			\Session::flash('flash_message', 'IP ban not found.');
		}
		return redirect('/admin/ipbans');
	}
	
	public function showYesNoDecider()
	{
		$result = ord(openssl_random_pseudo_bytes(1)) >= 0x80;
		return view('admin.yesno', ['result' => $result]);
	}
	
	public function showUserList()
	{
		$this->preventLimited();

		$users = User::where('id', '!=', '1')->get();
		return view('admin.userlist', ['users' => $users]);
	}
	
	public function banUserToggle(Request $request)
	{
		$this->preventLimited();

		if ($request->user) {
			$user = User::where('name', $request->user)->first();
			if ($user) {
				if ($user->banned) {
					$user->banned = false;
					$user->save();
					return "B";
				} else {
					$user->banned = true;
					$user->save();
					return "U";
				}
			} else {
				return "Invalid user";
			}
		}
		
		return "No user specified";
	}
	
	public function showAssetModPage(Request $request)
	{
		$show_type = "all";
		$assets = null;

		if($request->type && $request->type == "nonapproved") {
			$assets = Asset::where('moderated', 0)->orderBy('created_at', 'desc')->get();
			$show_type = "nonapproved";
		} else {
			$assets = Asset::orderBy('created_at', 'desc')->get();
		}

		return view('admin.assetmod', ['assets' => $assets, 'show_type' => $show_type]);
	}

	public function changeAssetModStatus(Request $request)
	{
		if($request->action != "D" && $request->action != "A") {
			return "Invalid action";
		}

		$asset = Asset::where('id', $request->asset)->first();
		if($asset) {
			/*
			 * forsale column values
			 *  0: not for sale
			 *  1: for sale
			 *  2: disapproved, originally not for sale
			 *  3: disapproved, originally for sale
			 * */
			if($request->action == "D") {
				$asset->moderated = 2;
				
				if($asset->forsale == 0) {
					$asset->forsale = 2;
				} else {
					$asset->forsale = 3;
				}
			} else {
				$asset->moderated = 1;

				if($asset->forsale == 3) {
					$asset->forsale = 1;
				} else {
					$asset->forsale = 0;
				}
			}
			$asset->save();
			return $request->action;
		}
		return "Invalid asset";
	}
}
