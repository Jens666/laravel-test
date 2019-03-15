<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class UserReservation extends Model
{
    protected $table = 'user_reservations';

    private $maxGuestsPrReservation = 10;
    private $maxGuestsInTotal = 20;


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


    public function isReservationAvailable($numberOfGuests, $bookedFrom, $bookedUntil) {
        if ($numberOfGuests > $this->maxGuestsPrReservation) {
            return false;
        }

        $currentBookings = $this->where('booked_from', '<', $bookedUntil)
            ->where('booked_until', '>', $bookedFrom)
            ->get();

        $seatsTaken = 0;
        foreach ($currentBookings as $booking) {
            $seatsTaken += $booking->number_of_guests;
        }
        return $numberOfGuests + $seatsTaken <=  $this->maxGuestsInTotal;
    }

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     *
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];*/
}
