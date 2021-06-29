<?php

namespace App\Http\Controllers;

use App\Hat;

use App\ThumbnailQueue;
use App\User;

use Illuminate\Http\Request;

use abeautifulsite\SimpleImage;

use App\OwnedAsset;

use \Auth;

use Illuminate\Support\Facades\Storage;

use App\Asset;

class CatalogController extends Controller
{
	public function showCatalog(Request $request)
	{
		$type = "hat";
		switch($request->type) {
			case "hat":
			default:
				$type = "hat";
				break;
			case "tshirts":
				$type = "tshirt";
				break;
			case "shirts":
				$type = "shirt";
				break;
			case "pants":
				$type = "pants";
				break;
			case "faces":
				$type = "face";
				break;
			case "gear":
				$type = "gear";
				break;
		}

		$assets = null;

		$assets = Asset::where(['type' => $type, 'moderated' => 1, 'forsale' => 1])->orderBy('created_at', 'desc')->paginate(6);
		return view('catalog.show', ['assets' => $assets, 'type' => $type]);
	}

	public function uploadAsset(Request $request)
	{
		//$img = new SimpleImage($_FILES['asset_upload']['tmp_name']);
		//$img->best_fit(200,200)->save("Asset/AssetModels/" . $newsid . '_th.jpg');

		if(Auth::user()->money < 5) {
			\Session::flash('flash_message_error', 'You do not have 5 Huefish!');
			return back();
		}

		$this->validate($request, [
			'name' => 'required',
			'price' => 'required|integer|min:0|max:1000',
			'upload' => 'required|image|valid_image',
			'type' => 'required|valid_asset_type',
		], [
			'type.valid_asset_type' => 'Please select a valid type.',
			'upload.required' => 'Please upload a file.',
			'upload.image' => 'Please upload a valid image.',
			'upload.valid_image' => 'Please upload a PNG or JPG file.',
			'price.min' => 'The price cannot be below 0.',
			'price.max' => 'The price cannot be above 1000.',
		]);

		$asset = new Asset;
		$asset->name = $request->name;
		$asset->description = (!empty($request->description) ? $request->description : "No description");
		$asset->price = $request->price;
		$asset->author = \Illuminate\Support\Facades\Auth::user()->id;
		$asset->moderated = 0;
		$asset->forsale = 3;
		$asset->type = $request->type;

		$asset->save();

		try {
			$img = new SimpleImage($request->file('upload'));

			switch ($request->type) {
				case "face":
				case "gear":
				case "tshirt":
				default:
					$img->best_fit(200, 200);
					break;
				case "shirt":
					$img->crop(165, 201, 424, 74);
					break;
				case "pants":
					$img->crop(217, 482, 371, 355);
					break;
			}

			$savePath = storage_path() . "/app/public/Asset/" . $asset->id . '_th.png';
			$img->save($savePath);
		}
		catch (Exception $ex) {
			Storage::disk('public')->copy('Asset/unknownThumb.png', 'Asset/' . $asset->id . '_th.png');
		}

		//Storage::disk('public')->put('Asset/' . $asset->id, $request->file('upload')->file);

		$request->file('upload')->move(storage_path() . "/app/public/Asset/", $asset->id);

		$owned_asset = new OwnedAsset();
		$owned_asset->asset_id = $asset->id;
		$owned_asset->user_id = Auth::user()->id;
		$owned_asset->wearing = 0;
		$owned_asset->save();

		$queue = new ThumbnailQueue;
		$queue->type = "asset";
		$queue->specimen = $asset->id;
		$queue->save();

		Auth::user()->money = Auth::user()->money - 5;
		Auth::user()->save();

		\Session::flash('flash_message', 'Successfully created. <a href="' . url('/item/' . $asset->id) . '">Click here to see it!</a>');

		return redirect('/catalog/new');
	}

	public function showItemPage($id) {
		$item = Asset::where('id', $id)->first();
		$ownedasset = OwnedAsset::where(['asset_id' => $id, 'user_id' => \Auth::user()->id])->first();

		return view('catalog.item', ['item' => $item, 'asset' => $ownedasset]);
	}

	public function buyItemFromPage($id) {
		$item = Asset::where('id', $id)->first();
		$ownedasset = OwnedAsset::where(['asset_id' => $id, 'user_id' => \Auth::user()->id])->first();

		if($item) {
			if(OwnedAsset::where(['asset_id' => $id, 'user_id' => \Auth::user()->id])->first()) {
				\Session::flash('flash_message_error', 'You already own this asset.</a>');
			} else {
				if($item->forsale == false) {
					\Session::flash('flash_message_error', 'This item is not for sale.</a>');
				} else {
					if (Auth::user()->money >= $item->price) {
						Auth::user()->money = Auth::user()->money - $item->price;
						Auth::user()->save();

						$author = User::where('id', $item->author)->first();
						$author->money = $author->money + round(($item->price / 1.25), 0, PHP_ROUND_HALF_DOWN);
						$author->save();

						$asset = new OwnedAsset;
						$asset->user_id = Auth::user()->id;
						$asset->asset_id = $item->id;
						$asset->wearing = 0;
						$asset->save();

						$ownedasset = $asset;

						\Session::flash('flash_message', 'You\'ve successfully purchased this.</a>');
					} else {
						\Session::flash('flash_message_error', 'Not enough money.</a>');
					}
				}
			}
		}

		return view('catalog.item', ['item' => $item, 'asset' => $ownedasset]);
	}

	public function showItemSettingsPage($id)
	{
		$item = Asset::where('id', $id)->first();
		if($item) {
			if($item->author == Auth::user()->id) {
				return view('catalog.settings', ['item' => $item]);
			}
		}
		return redirect('/catalog');
	}

	public function updateItemSettings($id, Request $request)
	{
		$item = Asset::where('id', $id)->first();
		if($item) {
			if($item->author == Auth::user()->id) {
				$this->validate($request, [
					'name' => 'required',
					'price' => 'required|integer|min:0|max:1000',
				], [
					'price.min' => 'The price cannot be below 0.',
					'price.max' => 'The price cannot be above 1000.',
				]);

				$item->name = $request->name;
				$item->price = $request->price;
				if(!$request->description) {
					$item->description = "No description";
				}

				$item->save();

				\Session::flash('flash_message', 'Updated settings.');

				return back();
			}
		}
		return redirect('/catalog');
	}
}
