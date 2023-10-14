<?php
namespace Dzialkowik\OpenWeather;

use Dzialkowik\Logger\Logger;
use Dzialkowik\Taxonomies\CityTax;
use stdClass;
use function Env\env;

class OWConfig {
	private $api_key;
	private $units;

	public function __construct() {
		$this->api_key = env('OW_API');
		$this->units   = 'metric';
	}

	public function get_open_weather_data( $lat_lng = array('lat' => 50.89973, 'lng' => 15.72899) ) {
		if ( ! $lat_lng ) {
			return false;
		}
		$url = 'https://api.openweathermap.org/data/2.5/weather?lat=' . $lat_lng['lat'] . '&lon=' . $lat_lng['lng'] . '&exclude=alerts&units=' . $this->units . '&appid=' . $this->api_key;
		return $this->check_request_response( json_decode( wp_remote_request( $url )['body'] ) );
	}

	public function check_request_response( $response ) {
		if( $response->cod !== 200 ) {
			$logger = new Logger();
			$logger->log( 'ERROR: ' . $response->cod . ' ' . $response->message );
			return false;
		}
		return $response;
	}

	public function get_cached_weather_data( $term_id ) {
		$get_open_weather_data = get_transient( 'get_open_weather_data_' . $term_id );
		if ( ! $get_open_weather_data ) {
			return $this->set_cached_weather_data( $term_id );
		}
		return $get_open_weather_data;
	}

	public function set_cached_weather_data( $term_id ) {
		set_transient( 'get_open_weather_data_' . $term_id, $this->request_for_weather_data( $term_id ), 18 * HOUR_IN_SECONDS );
		return $this->get_cached_weather_data( $term_id );
	}

	public function request_for_weather_data( $term_id ) {
		$city_tax = new CityTax();
		return $this->get_open_weather_data( $city_tax->get_city_coords( $term_id ) );
	}
}
