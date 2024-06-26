<?php
namespace Dzialkowik\OpenWeather;

use Dzialkowik\API\APIConfig;
use Dzialkowik\Logger\Logger;
use Dzialkowik\Taxonomies\CityTax;
use stdClass;
use function Env\env;

class OWConfig extends APIConfig {
	private $api_key;
	private $units;

	public function __construct() {
		$this->api_key = env( 'OW_API' );
		$this->units   = 'metric';
	}

	public function get_open_weather_data( $lat_lng = array(
		'lat' => 50.89973,
		'lng' => 15.72899,
	) ) {
		if ( ! $lat_lng ) {
			return false;
		}
		$open_weather_request_url = 'https://api.openweathermap.org/data/2.5/weather?lat=' . $lat_lng['lat'] . '&lon=' . $lat_lng['lng'] . '&exclude=alerts&units=' . $this->units . '&appid=' . $this->api_key;
		$open_weather_request     = wp_remote_request( $open_weather_request_url );
		return $this->check_request_response( $open_weather_request );
	}

	public function get_cached_weather_data( $term_id ) {
		$get_open_weather_data = get_transient( 'get_open_weather_data_' . $term_id );
		if ( ! $get_open_weather_data ) {
			return $this->set_cached_weather_data( $term_id );
		}

		return $this->check_request_decode( $get_open_weather_data );
	}


	public function set_cached_weather_data( $term_id ) {
		$request_for_weather_data = $this->request_for_weather_data( $term_id );
		set_transient( 'get_open_weather_data_' . $term_id, $request_for_weather_data, 18 * HOUR_IN_SECONDS );
		return $request_for_weather_data;
	}

	public function request_for_weather_data( $term_id ) {
		$city_tax        = new CityTax();
		$get_city_coords = $city_tax->get_city_coords( $term_id );
		if ( $get_city_coords ) {
			return $this->get_open_weather_data( $get_city_coords );
		}
		return false;
	}
}
