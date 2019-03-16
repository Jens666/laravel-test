<?php
namespace App\Http\Controllers;

use App\Drink;
use App\Http\Controllers\Controller;

class DrinksController extends Controller
{
	/**
     * Function that fetches an array of drinks based on the given $food parameter.
	 *
     * Failing to get anything that matches the given food, the function will look up
     * previously chosen drinks in local database and deliver the 10 top picks.
     *
     * @param string $food
     * @return array
     * @author Jens666
     */
	public function getDrink($food) {
		$food = str_replace(' ', '_', $food);
		$food = strtolower($food);
		$result = Drink::curlGet(['food' => $food]);

		$firstResultEmpty = empty($result);
		if ($firstResultEmpty) {
			$idCollection = Drink::join('user_reservations', 'drinks.id', '=', 'user_reservations.drink_id')
				->selectRaw('punk_api_id, COUNT(user_reservations.id) as times_ordered')
				->groupBy('drinks.id')
				->orderBy('times_ordered', 'DESC')
				->take(10)
				->get();

			$ids = [];
			foreach ($idCollection as $id) {
				$ids[] = $id->punk_api_id;
			}

			$ids = implode('|', $ids);
			$result = Drink::curlGet(['ids' => $ids]);
		}
	
		return [
			'first_result_empty' => $firstResultEmpty,
			'results' => $result
		];
	}
}
