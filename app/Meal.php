<?php
namespace App;

use App\CurlTrait;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use CurlTrait;

    protected $table = 'meals';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'the_meal_db_id',
		'name',
		'type'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Function that fetches a random meal through hardcoded url
     *
     * @return bool
     * @author Jens666
     */
    public static function curlGet() {
        $meal = self::curlCall('https://www.themealdb.com/api/json/v1/1/random.php');
        return json_decode($meal, true);
    }
}
