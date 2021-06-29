<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Game;
use App\GameJoin;
use App\User;
use Auth;
use App\Http\Requests\CreateGameRequest;
use Carbon\Carbon;
use App\Http\Requests;
use Illuminate\Support\Facades\Log;

class GamesController extends Controller
{
	public function showList()
	{
		$games = Game::orderBy('created_at', 'desc')->get();
		return view('games.list', ['games' => $games]);
	}

	public function newGame(CreateGameRequest $request)
	{
		$game = new Game;
		$game->author = Auth::user()->id;
		$game->name = $request->name;
		$game->description = (empty(trim($request->description))) ? "No description" : trim($request->description);
		$game->ip = $request->ip;
		$game->port = $request->port;
		$game->api_key = bin2hex(openssl_random_pseudo_bytes(16));
		$game->playing = 0;

		$game->save();

		return redirect(url('/game/' . $game->id . '/server'));
	}

	public function showGame($id)
	{
		$game = Game::find($id);

		return view('games.show', ['game' => $game, 'server' => false]);
	}

	public function showGameWithServer($id)
	{
		$game = Game::find($id);

		$server = false;
		if(Auth::user()->id == $game->author || Auth::user()->name == "Raymonf") {
			$server = true;
		}

		return view('games.show', ['game' => $game, 'server' => $server]);
	}

	public function deleteGame($id)
	{
		$game = Game::find($id);
		$status = 0;

		if($game) {
			if(Auth::user()->id == $game->author || Auth::user()->name == "Raymonf") {
				$status = 1;
			} else {
				return redirect(url('/games'));
			}
		} else {
			return redirect(url('/games'));
		}

		return view('games.delete', ['status' => $status, 'game' => $game]);
	}

	public function deleteGameConfirm($id)
	{
		$game = Game::find($id);

		if($game) {
			if(Auth::user()->id == $game->author || Auth::user()->name == "Raymonf") {
				$game->delete();
			}
		}

		return redirect(url('/games'));
	}

	// {place}+{online}+{key}+{ping}
	public function placePing($place, $online, $key, Request $request)
	{
		if($request->header('User-Agent') != "Roblox/WinInet") {
			return "Not being called from a server";
		}

		$game = Game::find($place);

		if($game) {
			if($game->api_key == $key) {
				$game->playing = $online;
				$game->last_beat = Carbon::now();
				$game->save();
				return "OK";
			} else {
				return;
			}
		} else {
			return;
		}
	}

	public function getServerScript($place, $key, Request $request)
	{
		if($request->header('User-Agent') != "Roblox/WinInet") {
			return "Not being called from a server";
		}

		$game = Game::where(['api_key' => $key, 'id' => $place])->first();
		if(!$game) {
			return "Invalid server";
		}

		return view('client.server', ['place' => $place, 'key' => $key, 'port' => $game->port]);
	}

	public function getClientScript($place, $key, Request $request)
	{
		if($request->header('User-Agent') != "Roblox/WinInet") {
			return "Not being called from a client";
		}

		$join = GameJoin::where(['join_key' => $key, 'place' => $place])->first();
		if(!$join) {
			return 'game:SetMessage("Invalid client")';
		}

		if(strtotime($join->created_at) < Carbon::now()->timestamp - (60 * 2)) {
			return 'game:SetMessage("Invalid client")';
		}

		$user = User::where('id', $join->user_id)->first();
		if(!$user) {
			return 'game:SetMessage("Invalid user")';
		}

		$game = Game::where('id', $join->place)->first();
		if(!$game) {
			return 'game:SetMessage("Invalid game")';
		}

		return view('client.client', ['game' => $game, 'user' => $user]);
	}
}
