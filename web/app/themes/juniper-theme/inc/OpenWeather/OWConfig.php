<?php
namespace Dzialkowik\OpenWeather;

use function Env\env;

class OWConfig {
	private $api_key;
	private $units;

	public function __construct() {
		$this->api_key = env('OW_API');
		$this->units   = 'metric';
	}

	public function get_open_weather_data( $lat, $lng ) {
		if ( ! $lat && ! $lng ) {
			return false;
		}
		$url = 'https://api.openweathermap.org/data/2.5/weather?lat=' . $lat . '&lon=' . $lng . '&exclude=alerts&units=' . $this->units . '&appid=' . $this->api_key;
		return wp_remote_request( $url );
	}
}
