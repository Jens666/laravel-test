<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class UserReservation extends Model
{
    protected $table = 'user_reservations';

    private $maxGuestsInTotal = 20;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'number_of_guests',
        'drink_id',
        'meal_id',
        'booked_from',
        'booked_until'
    ];


    /**
     * Function that determines if there is room for a given number of guests within a given
     * timespan.
     *
     * Before calculating whether there is room for the reservation, the function assures that
     * the given $bookedFrom are not a date/time that has passed already
     *     
     * time : 16    18    20    22
     *         |__2__|__4__|__4__|
     *         |_____4_____|     |
     *               |_____3_____|
     *            |__8__|
     *
     * If the user were to place a reservation between 16 and 18 the function would calculate
     * that 14 seats were taken - thus there would be room for 6. Looking to place a reservation
     * between 20 and 22 the function would calculate 8 seats to be taken.
     *
     * The example above illustrates cases where the timespan for bookings may vary.
     *
     * @param int $numberOfGuests
     * @param datetime $bookedFrom
     * @param datetime $bookedUntil
     * @return bool
     * @author Jens666
     */
    public function isReservationAvailable($numberOfGuests, $bookedFrom, $bookedUntil) {
        $bookingTime = strtotime($bookedFrom);
        if ($bookingTime < time()) {
            return false;
        }

        $currentBookings = $this->where('booked_from', '<', $bookedUntil)
            ->where('booked_until', '>', $bookedFrom)
            ->get();

        $seatsTaken = 0;
        foreach ($currentBookings as $booking) {
            // Determine $seatsTaken by the definition that 9 guests actually occupy 10 seats
            $remainder = $booking->number_of_guests % 2;
            $seatsTaken += $booking->number_of_guests + $remainder;
        }
        return $numberOfGuests + $seatsTaken <= $this->maxGuestsInTotal;
    }
}
