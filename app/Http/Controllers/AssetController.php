<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Hat;

use App\Http\Requests;

use Storage;

class AssetController extends Controller
{
	public function getAsset(Request $request)
	{
		if($request->id) {
			if(Storage::disk('public')->exists('Asset/' . intval($request->id))) {
				$response = \Response::make(Storage::disk('public')->get('Asset/' . intval($request->id)), 200);
				$response->header('Content-Type', 'application/octet-stream');
				return $response;
			} else {
				// is it a hat?
				$hat = Hat::where('asset_id', $request->id)->first();
				if($hat) {
					return redirect($hat->model_url);
				}
			}

			return redirect("http://assets.rblxdev.pw/Asset/?id=" . intval($request->id));
		}
		else
		{
			return "No ID specified";
		}

		return "Invalid asset";
	}

	public function getAssetThumbnail($id, Request $request)
	{
		// is it a hat?
		$hat = Hat::where('asset_id', $id)->first();
		if($hat) {
			return redirect($hat->thumbnail_url);
		}

		if(!$request->nomod) {
			$asset = \App\Asset::where('id', $id)->first();
			if ($asset) {
				if ($asset->moderated != 1) {
					$response = \Response::make(Storage::disk('public')->get('Asset/unapprovedAsset.png'), 200);
					$response->header('Content-Type', 'application/octet-stream');
					return $response;
				}
			}
		}

		if(Storage::disk('public')->exists('Thumbnails/asset_' . intval($id) . '_thumb.png')) {
			$response = \Response::make(Storage::disk('public')->get('Thumbnails/asset_' . intval($id) . '_thumb.png'), 200);
			$response->header('Content-Type', 'application/octet-stream');
			return $response;
		} else {
			if(Storage::disk('public')->exists('Asset/' . intval($id) . '_th.png')) {
				$response = \Response::make(Storage::disk('public')->get('Asset/' . intval($id) . '_th.png'), 200);
				$response->header('Content-Type', 'application/octet-stream');
				return $response;
			}
		}
		return "No thumbnail";
	}

	public function getTShirt($id)
	{
		$asset = new \stdClass();
		$asset->type = "ShirtGraphic";
		$asset->templateName = "Graphic";
		$asset->templateUrl = url('/Asset/?id=' . $id);
		$asset->name = "Shirt Graphic";
		
		return view("gameclient.data.asset", compact('asset'));
	}

	public function getShirt($id)
	{
		$asset = new \stdClass();
		$asset->type = "Shirt";
		$asset->templateName = "ShirtTemplate";
		$asset->templateUrl = url('/Asset/?id=' . $id);
		$asset->name = "Shirt";
		
		return view("gameclient.data.asset", compact('asset'));
	}

	public function getPants($id)
	{
		$asset = new \stdClass();
		$asset->type = "Pants";
		$asset->templateName = "PantsTemplate";
		$asset->templateUrl = url('/Asset/?id=' . $id);
		$asset->name = "Pants";
		
		return view("gameclient.data.asset", compact('asset'));
	}

	public function getFace($id)
	{
		$asset = new \stdClass();
		$asset->templateUrl = url('/Asset/?id=' . $id);
		
		return view("gameclient.data.asset_face", compact('asset'));
	}
}
