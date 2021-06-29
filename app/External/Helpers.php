<?php
	// Global functions

	function getAssetTypeFriendlyName($type) {
		switch($type) {
			default:
				return $type;
				break;
			case "tshirts":
			case "tshirt":
				return "T-Shirt";
				break;
			case "shirts":
			case "shirt":
				return "Shirt";
				break;
			case "pants":
				return "Pants";
				break;
			case "hat":
				return "Hat";
				break;
			case "face":
			case "faces":
				return "Face";
				break;
			case "gear":
				return "Gear";
				break;
			case "mesh":
				return "Mesh";
				break;
			case "texture":
				return "Texture";
				break;
		}
	}

	function getAssetTypeFriendlyNamePlural($type) {
		switch($type) {
			default:
				return $type;
				break;
			case "tshirts":
			case "tshirt":
				return "T-Shirts";
				break;
			case "shirts":
			case "shirt":
				return "Shirts";
				break;
			case "pants":
				return "Pants";
				break;
			case "hat":
				return "Hats";
				break;
			case "face":
			case "faces":
				return "Faces";
				break;
			case "gear":
				return "Gear";
				break;
			case "mesh":
				return "Meshes";
				break;
			case "textures":
				return "Textures";
				break;
		}
	}

	function checkDisposableMail($email) {
		include(__DIR__ . '/DisposableEmailChecker.php');
		return DisposableEmailChecker::is_disposable_email($email);
	}

