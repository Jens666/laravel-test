<?php
namespace App\Http\Controllers;

use App\Drink;
use App\Meal;
use App\User;
use App\UserReservation;
use DateTime;
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
        $bookedUntil = new DateTime($bookedFrom);
        $bookedUntil = $bookedUntil->modify('+2 hour');
        $bookedUntil = $bookedUntil->format('Y-m-d H:i:s');

        $errors = $this->validateTime($bookedFrom);
        $tooFewGuests = $numberOfGuests < 2;
        $tooManyGuests = $numberOfGuests > 10;

        if (!empty($errors) || $tooManyGuests || $tooFewGuests) {
            if ($tooManyGuests) {
                $errors[] = 'You cannot have more than 10 guests in 1 reservation';
            } elseif ($tooFewGuests) {
                $errors[] = 'You cannot have fewer than 2 guests in 1 reservation';
            }
            return view('user_reservation')->withErrors($errors);
        }

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
            return view('user_reservation', [
                'from_error' => true,
                'email' => $request->email,
				'number_of_guests' => $numberOfGuests,
                'booked_from' => $bookedFrom
            ])->withErrors([
                'There is no room for ' . $numberOfGuests . ' people in the given time ' . $bookedFrom . '.'
            ]);
        }
    }

    /**
     * Function that returns the user_reservation view wit pre-filled data
     *
     * Handling the request object the system checks if it is possible to do the reservation..
     * If so, internal ids are fetched for meals, drinks and the user in question - and
     * finally the reservation is placed
     *
     * @param $request Request
     * @return view
     * @author Jens666
     */
    public function getNextAvailable(Request $request) {
        $numberOfGuests = $request->number_of_guests;
        $bookedFrom = $request->booked_from;
        $bookedUntil = new DateTime($bookedFrom);

        $hour = $bookedUntil->format('H');
        $timeModifier = $hour % 2 == 0 ? '+2 hour' : '+1 hour';

        $bookedUntil = $bookedUntil->modify($timeModifier);
        $bookedUntil = $bookedUntil->format('Y-m-d H:i:s');
        $userReservation = new UserReservation;
        $bookedFrom = $this->getNextRecursive($userReservation, $numberOfGuests, $bookedFrom, $bookedUntil);

        return view('user_reservation', [
            'from_error' => false,
            'email' => $request->email,
            'number_of_guests' => $numberOfGuests,
            'booked_from' => $bookedFrom
        ]);
    }

    /**
     * Function that recursively finds the next available 2 hour slot for a given number of guests
     * First - make sure the requested time is within the opening hours - if not - advance time with
     * 2 hours and try again.
     * If we are inside the opening hours, check if the requested time is available
     * - if avalable - return time
     * If nothing is available - advance time by 2 hours and try again.
     *
     * @param $request Request
     * @return view
     * @author Jens666
     */
    private function getNextRecursive($userReservation, $numberOfGuests, $bookedFrom, $bookedUntil) {
        $errors = $this->validateTime($bookedFrom);

        if (!empty($errors)) {
            $times = $this->returnPlusTwoHours($bookedUntil);
            return $this->getNextRecursive($userReservation, $numberOfGuests, $times[0], $times[1]);
		}

        if ($userReservation->isReservationAvailable($numberOfGuests, $bookedFrom, $bookedUntil))	 {
            return $bookedFrom;
        } else {
            $times = $this->returnPlusTwoHours($bookedUntil);
            return $this->getNextRecursive($userReservation, $numberOfGuests, $times[0], $times[1]);
        }
    }

    /**
     * Function that takes its given time, returns the time along with the time plus 2 hours
     *
     * @param string $bookedUntil
     * @return view
     * @author Jens666
     */
    private function returnPlusTwoHours($bookedUntil) {
        $bookedFrom = $bookedUntil;
        $bookedUntil = new DateTime($bookedFrom);
        $bookedUntil = $bookedUntil->modify('+2 hour');
        $bookedUntil = $bookedUntil->format('Y-m-d H:i:s');
        return [$bookedFrom, $bookedUntil];
    }

    /**
     * See if given time plus 2 hours is within opening hours
     *
     * @param string $booked_from
     * @return view
     * @author Jens666
     */
    private function validateTime($bookedFrom) {
        $bookedFrom = new DateTime($bookedFrom);
        $from = (int) $bookedFrom->format('H');
        $errors = [];

        if ($from > 20 || $from < 16) {
            $errors[] = 'Reservations should be made between 16:00 and 20:00';
        }

        return $errors;
    }
}
