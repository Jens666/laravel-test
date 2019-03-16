<?php
namespace App;

use App\CurlTrait;
use Illuminate\Database\Eloquent\Model;

class Drink extends Model
{
    use CurlTrait;

    protected $table = 'drinks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'punk_api_id',
		'name',
		'description',
		'tagline'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public static function curlGet($params) {
        $defaultParams = [
            'per_page' => 10
        ];
        $params = array_merge($defaultParams, $params);
        $queryString = http_build_query($params);
        $drinks = self::curlCall('https://api.punkapi.com/v2/beers?' . $queryString);

        return json_decode($drinks, true);
    }

    public static function findOrCreate($id) {
        $preExist = self::where('punk_api_id', '=', $id)->first();

        if (empty($preExist)) {
            $drink = self::curlCall('https://api.punkapi.com/v2/beers/' . $id);
            $drink = json_decode($drink, true);
            $new = self::create([
                'punk_api_id' => $drink[0]['id'],
                'name' => utf8_encode($drink[0]['name']),
                'description' => utf8_encode($drink[0]['description']),
                'tagline' => utf8_encode($drink[0]['tagline'])
            ]);

            return $new->id;
        } else {
            return $preExist->id;
        }
    }
}
