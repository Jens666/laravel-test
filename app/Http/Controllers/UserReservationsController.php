<?php
namespace App\Http\Controllers;

use App\User;
use App\UserReservation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserReservationsController extends Controller
{
	public function store(Request $request) {
		$user = User::firstOrCreate(['email' => $request->email]);
		if (!$user->wasRecentlyCreated) {
			$user->times_returned++;
			$user->save();
		}

		$userReservation = new UserReservation;
		if ($userReservation->isReservationAvailable($request->number_of_guests, $request->booked_from, $request->booked_until)) {
			$userReservation->user_id = $user->id;
			$userReservation->number_of_guests = $request->number_of_guests;
			$userReservation->booked_from = $request->booked_from;
			$userReservation->booked_until = $request->booked_until;
			$userReservation->save();
		} else {
			return view('user_reservation')->withErrors('lal');
		}

	}

	public function index() {
		return view('user_reservation');
	}

	public function testReservationCheck($nog, $bfrom, $buntil) {
		$userReservation = new UserReservation;

		if ($userReservation->isReservationAvailable($nog, $bfrom, $buntil)) {
			var_dump('SHIT works');
		} else {
			var_dump('No way');
		}
	}
}
