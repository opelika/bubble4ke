<?php

namespace App\Http\Controllers;

use App\Asset;

use App\ThumbnailQueue;
use Illuminate\Http\Request;

use App\Http\Requests;

use App\GameJoin;

use App\OwnedAsset;

use App\Hat;

use App\User;

use App\Game;

use Auth;

use Carbon\Carbon;

use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
	public function makeToken($id) {
		$game = Game::find($id);

		if($game) {
			$token = new GameJoin;
			$token->user_id = Auth::user()->id;
			$token->place = intval($id);
			$token->join_key = bin2hex(openssl_random_pseudo_bytes(32));
			$token->save();
			return $token->join_key;
		}

		return "Invalid place";
	}

	// wat
	/*public function checkToken($key, $user) {

		$user = User::where('name', $user)->first();

		if($user) {
			$token = $user->joinTokens()->orderBy('created_at', 'desc')->first();

			if ($token) {
				if ($token->join_key == $key) {
					// 60 seconds in a minute; 2 minutes
					if (strtotime($token->created_at) > Carbon::now()->timestamp - (60 * 2)) {
						return "true";
					}
				}
			}
		}
		return "false";
	}*/

	// For client to verify user's join validity
	public function checkToken($place, $key) {
		$join = GameJoin::where('join_key', $key)->first();
		if(!$join) {
			return "false";
		}

		if($join->place == $place) {
			// 60 seconds in a minute; 2 minutes
			if(strtotime($join->created_at) > Carbon::now()->timestamp - (60 * 2)) {
				return "true";
			}
		}

		return "false";
	}

	// For server to verify user's join validity
	public function checkUser($player, $place) {
		$user = User::where('name', $player)->first();
		if(!$user) {
			return "false";
		}

		$join = GameJoin::where('user_id', $user->id)->orderBy('created_at', 'desc')->first();
		if(!$join) {
			return "false";
		}

		if($join->place == $place) {
			// 60 seconds in a minute; 2 minutes
			if(strtotime($join->created_at) > Carbon::now()->timestamp - (60 * 2)) {
				return "true";
			}
		}

		return "false";
	}

	public function getCharApp($user)
	{
		$charapp = array();
		$charapp[] = url('/user/getbodycolors/' . $user);

		$user = User::where('id', $user)->first();
		if($user) {
			$ownedassets = OwnedAsset::where(['user_id' => $user->id, 'wearing' => true])->get();
			foreach($ownedassets as $ownedasset) {
				$asset = Asset::where('id', $ownedasset->asset_id)->first();
				if($asset && $asset->moderated == 1) {
					switch($asset->type) {
						case "shirt":
							$charapp[] = url('/user/getshirt/' . $asset->id);
							break;
						case "tshirt":
							$charapp[] = url('/user/gettshirt/' . $asset->id);
							break;
						case "pants":
							$charapp[] = url('/user/getpants/' . $asset->id);
							break;
						case "hat":
							$hat = Hat::where('asset_id', $asset->id)->first();
							$charapp[] = $hat->model_url;
							break;
						case "face":
							$charapp[] = url('/user/getface/' . $asset->id);
							break;
						case "gear":
							$charapp[] = url("/Asset/?id=" . $asset->id);
							break;
						default:
							break;
					}
				}
			}
		}

		return join(';', $charapp);
	}

	public function getCharAppThumb($user)
	{
		$charapp = array();

		$user = User::where('name', $user)->first();
		if($user) {
			$charapp[] = url('/user/getbodycolors/' . $user->id);

			$ownedassets = OwnedAsset::where(['user_id' => $user->id, 'wearing' => true])->get();
			foreach($ownedassets as $ownedasset) {
				$asset = Asset::where('id', $ownedasset->asset_id)->first();
				if($asset && $asset->moderated == 1) {
					switch($asset->type) {
						case "shirt":
							$charapp[] = url('/user/getshirt/' . $asset->id);
							break;
						case "tshirt":
							$charapp[] = url('/user/gettshirt/' . $asset->id);
							break;
						case "pants":
							$charapp[] = url('/user/getpants/' . $asset->id);
							break;
						case "hat":
							$hat = Hat::where('asset_id', $asset->id)->first();
							$charapp[] = $hat->model_url;
							break;
						case "face":
							$charapp[] = url('/user/getface/' . $asset->id);
							break;
						case "gear":
							$charapp[] = url("/Asset/?id=" . $asset->id);
							break;
						default:
							break;
					}
				}
			}
		}

		return join(';', $charapp);
	}

	public function getAssetCharAppThumb($id)
	{
		$asset = Asset::where('id', $id)->first();
		if($asset) {
			switch($asset->type) {
				case "shirt":
					return url('/user/getshirt/' . $asset->id);
					break;
				case "tshirt":
					return url('/user/gettshirt/' . $asset->id);
					break;
				case "pants":
					return url('/user/getpants/' . $asset->id);
					break;
				case "hat":
					$hat = Hat::where('asset_id', $asset->id)->first();
					return $hat->model_url;
					break;
				case "face":
					return url('/user/getface/' . $asset->id);
					break;
				case "gear":
					$charapp[] = url("/Asset/?id=" . $asset->id);
					break;
				default:
					break;
			}
		}

		return url("/Asset/?id=" . $id);
	}

	public function getThumbnailQueue()
	{
		$queue = array();

		$thumbs = \App\ThumbnailQueue::all();

		foreach($thumbs as $thumb) {
			if($thumb->type == "asset") {
				$queue[] = "asset:" . $thumb->specimen;
			} else {
				$queue[] = "user:" . $thumb->specimen;
			}
		}

		return join(';', $queue);
	}

	public function regenThumbnail()
	{
		$thumb = \App\ThumbnailQueue::where(['type' => 'user', 'specimen' => Auth::user()->name])->first();

		if(!$thumb) {
			$nque = new \App\ThumbnailQueue;
			$nque->type = "user";
			$nque->specimen = Auth::user()->name;
			$nque->save();
		}

		return "OK";
	}

	public function regenAllThumbnailsDebug()
	{
		$assets = Asset::all();
		foreach($assets as $asset)
		{
			$nq = new \App\ThumbnailQueue;
			$nq->type = "asset";
			$nq->specimen = $asset->id;
			$nq->save();
		}

		return "done";
	}

	public function removeFromQueue(Request $request)
	{
		$deleteStatus = false;

		if($request->id && $request->type) {
			if($request->type == "asset") {
				$asset = ThumbnailQueue::where(['type' => 'asset', 'specimen' => $request->id])->first();
				if($asset) {
					$asset->delete();
					$deleteStatus = true;
				}
			} else {
				$user = ThumbnailQueue::where(['type' => 'user', 'specimen' => $request->id])->first();
				if($user) {
					$user->delete();
					$deleteStatus = true;
				}
			}
		}

		return $deleteStatus ? "true" : "false";
	}

	public function uploadThumbnail(Request $request)
	{
		if ($request->hasFile('file')) {
			$name = $request->file('file')->getClientOriginalName();
			$request->file('file')->move(storage_path() . "/app/public/Thumbnails/", $name);
			return "true";
		}
		return "false";
	}

	public function getThumbnail()
	{
		if(\Storage::disk('public')->exists('Thumbnails/user_' . Auth::user()->name . '_thumb.png')) {
			$response = \Response::make(\Storage::disk('public')->get('Thumbnails/user_' . Auth::user()->name . '_thumb.png'), 200);
			$response->header('Content-Type', 'application/octet-stream');
			return $response;
		}

		$response = \Response::make(\Storage::disk('public')->get('Thumbnails/user_basicCharacter.png'), 200);
		$response->header('Content-Type', 'application/octet-stream');
		return $response;
	}

	public function showOwnedAssets($type) {
		if($type != "shirts" && $type != "tshirts" && $type != "pants" && $type != "hats" && $type != "faces" && $type != "gear") {
			return redirect('/character');
		}

		$internal_type = "hat";
		switch($type) {
			default:
			case "hats":
				$internal_type = "hat";
				break;
			case "tshirts":
				$internal_type = "tshirt";
				break;
			case "shirts":
				$internal_type = "shirt";
				break;
			case "pants":
				$internal_type = "pants";
				break;
			case "faces":
				$internal_type = "face";
				break;
			case "gear":
				$internal_type = "gear";
				break;
		}

		$assets = \DB::table('owned_assets')
			->join('assets', 'owned_assets.asset_id', '=', 'assets.id')
			->where('owned_assets.user_id', Auth::user()->id)
			->where('assets.type', $internal_type)
			->where('assets.moderated', 1)
			->select('assets.id', 'assets.name', 'assets.type', 'owned_assets.wearing')
			->orderBy('owned_assets.created_at', 'desc')
			->get();

		return view('character.inventory', ['assets' => $assets, 'type' => $internal_type]);
	}

	public function toggleUserItem($id)
	{
		$ownstatus = OwnedAsset::where(['asset_id' => $id, 'user_id' => Auth::user()->id]);

		if($ownstatus) {
			if($ownstatus->first()->wearing == false) {

				$internal_type = Asset::whereId($id)->first()->type;

				$assets = \DB::table('owned_assets')
					->join('assets', 'owned_assets.asset_id', '=', 'assets.id')
					->where('owned_assets.user_id', Auth::user()->id)
					->where('assets.type', $internal_type)
					->select('owned_assets.id', 'owned_assets.wearing')
					->get();

				foreach($assets as $asset) {
					if($asset->wearing == true) {
						$oa = OwnedAsset::where(['id' => $asset->id, 'user_id' => Auth::user()->id])->first();
						$oa->wearing = false;
						$oa->save();
					}
				}

				$asset = $ownstatus->first();
				$asset->wearing = true;
				$asset->save();
			} else {
				$asset = $ownstatus->first();
				$asset->wearing = false;
				$asset->save();
			}
		}

		return back();
	}

	public function showUserProfile($id, Request $request)
	{
		$user = User::where(['id' => $id, 'banned' => 0])->first();

		$paneltype = "primary";

		if($user)
		{

			if ($user->isMod()) {
				$paneltype = "info";
			}

			if ($user->isAdmin()) {
				$paneltype = "danger";
			}
			
		}

		return view('user.profile', compact(['user', 'paneltype']));
	}

	public function getUserThumbnail($id)
	{
		$user = User::find($id);
		if(!$user)
		{
			return "Invalid ID.";
		}

		if(\Storage::disk('public')->exists('Thumbnails/user_' . $user->name . '_thumb.png')) {
			$response = \Response::make(\Storage::disk('public')->get('Thumbnails/user_' . $user->name . '_thumb.png'), 200);
			$response->header('Content-Type', 'application/octet-stream');
			return $response;
		}

		$response = \Response::make(\Storage::disk('public')->get('Thumbnails/user_basicCharacter.png'), 200);
		$response->header('Content-Type', 'application/octet-stream');
		return $response;
	}
	
	public function showUserList(Request $request)
	{
		$users = null;

		$backtext = "";

		if(!$request->q) {
			$users = User::where('banned', 0)->orderBy('last_visit', 'DESC')->paginate(10);
		} else {
			if(empty(trim($request->q))) {
				$users = User::where('banned', 0)->orderBy('last_visit', 'DESC')->paginate(10);
			} else {
				$users = User::where('name', 'LIKE', '%' . e($request->q) . '%')->where('banned', 0)->paginate(10);
				$backtext = '<p><a href="' . url('/users') . '">Clear search &rsaquo;</a></p>';
			}
		}

		return view('user.search', compact(['users', 'backtext']));
	}

	public function changeBlurb(Request $request)
	{
		$this->validate($request, [
			'blurb' => 'max:1000',
		]);

		Auth::user()->blurb = trim(preg_replace('/\pM/u', '', $request->blurb));
		Auth::user()->save();

		\Session::flash('flash_message', 'Saved blurb.');

		return back();
	}
}
