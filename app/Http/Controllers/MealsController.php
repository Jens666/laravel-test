<?php
namespace App\Http\Controllers;

use App\Meal;
use App\Http\Controllers\Controller;

class MealsController extends Controller
{
    /**
     * Function that through model-curl-trait connection fetches a random meal
     *
     * @return array
     * @author Jens666
     */
	public function getRandomMeal() {
		$meal = Meal::curlGet();
		$meal = $meal['meals'][0];
		
		return [
			'id' => $meal['idMeal'],
			'name' => $meal['strMeal'],
			'type' => $meal['strCategory']
		];
	}
}
