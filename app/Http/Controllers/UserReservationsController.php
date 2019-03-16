<?php
namespace App\Http\Controllers;

use App\Drink;
use App\Meal;
use App\User;
use App\UserReservation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserReservationsController extends Controller
{
	/**
     * Index function that returns the user_reservation view
     *
     * @return view
     * @author Jens666
     */
	public function index() {
		return view('user_reservation');
	}

	/**
     * Store function that returns the user_reservation view
	 *
	 * Handling the request object the system checks if it is possible to do the reservation..
	 * If so, internal ids are fetched for meals, drinks and the user in question - and
	 * finally the reservation is placed
     *
     * @param $request Request
     * @return view
     * @author Jens666
     */
	public function store(Request $request) {
		$numberOfGuests = $request->number_of_guests;
		$bookedFrom = $request->booked_from;
		$bookedUntil = $request->booked_until;

		$userReservation = new UserReservation;
		if ($userReservation->isReservationAvailable($numberOfGuests, $bookedFrom, $bookedUntil)) {

			$drinkId = Drink::findOrCreate($request->drink_id);
			$meal = Meal::firstOrCreate(
				[
					'the_meal_db_id' => $request->the_meal_db_id
				],
				[
					'name' => $request->meal_name,
					'type' => $request->meal_type
				]
			);

			$user = User::firstOrCreate(['email' => $request->email]);
			if (!$user->wasRecentlyCreated) {
				$user->times_returned++;
				$user->save();
			}
			$userReservation->user_id = $user->id;
			$userReservation->meal_id = $meal->id;
			$userReservation->drink_id = $drinkId;
			$userReservation->number_of_guests = $numberOfGuests;
			$userReservation->booked_from = $bookedFrom;
			$userReservation->booked_until = $bookedUntil;
			$userReservation->save();
			return view('user_reservation');
		} else {
			return view('user_reservation')->withErrors('lal');
		}
	}
}
