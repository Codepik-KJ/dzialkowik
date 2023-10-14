<?php
namespace Dzialkowik\OpenWeather;

class OWConfig {
	private $api_key;
	private $units;

	public function __construct() {
		$this->api_key = '2eec5593db3caa3b5ee7d2e08ed12124';
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
