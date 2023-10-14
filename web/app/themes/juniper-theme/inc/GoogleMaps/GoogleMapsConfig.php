<?php
namespace Dzialkowik\GoogleMaps;

use Dzialkowik\Logger\Logger;
use function Env\env;

class GoogleMapsConfig {
	private $api_key;

	public function __construct() {
		$this->api_key = env('GOOGLE_API');
	}

	public function get_google_maps_coords( $address ) {
		if ( ! $address ) {
			return false;
		}
		$url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $address . '&key=' . $this->api_key;
		return $this->check_request_response( json_decode( wp_remote_request( $url )['body'] ) );
	}

	public function check_request_response( $response ) {
		if( $response->status !== 'OK' ) {
			$logger = new Logger();
			$logger->log( 'ERROR: ' . $response->status . ' ' . $response->error_message );
			return false;
		}
		return $response;
	}

	public function request_for_google_maps_data( $city ) {
		$response = $this->get_google_maps_coords( $city );
		if ( ! $response ) {
			return false;
		}
		return $response->results[0]->geometry->location->lat . ',' . $response->results[0]->geometry->location->lng;
	}

}
