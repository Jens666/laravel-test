<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CurlTrait;

class DrinksController extends Controller
{

	use CurlTrait;
	
	public function getDrink($extension) {
		$drink = $this->curlCall('https://api.punkapi.com/v2/' . $extension);
		$drink = json_decode($drink, true);
	
		var_dump($drink);
	}
}
