<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CurlTrait;

class MealsController extends Controller
{

	use CurlTrait;
	
	public function getRandomMeal() {
		$meal = $this->curlCall('https://www.themealdb.com/api/json/v1/1/random.php');
		$meal = json_decode($meal, true);
		$meal = $meal['meals'][0];

		var_dump($meal['strMeal']);

		return [
			'id' => $meal['idMeal'],
			'name' => $meal['strMeal'],
			'type' => $meal['strCategory']
		];
	}
}
