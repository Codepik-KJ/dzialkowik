<?php
namespace Dzialkowik\GoogleMaps;

use Dzialkowik\Logger\Logger;
use function Env\env;

class GoogleMapsConfig {
	private $api_key;

	public function __construct() {
		$this->api_key = env( 'GOOGLE_API' );
	}

	public function get_google_maps_coords( $address ) {
		if ( ! $address ) {
			return false;
		}

		$google_maps_request_url    = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $address . '&key=' . $this->api_key;
		$google_maps_request        = wp_remote_request( $google_maps_request_url )['body'];
		$google_maps_request_decode = json_decode( $google_maps_request );
		if ( ! is_object( $google_maps_request_decode ) ) {
			$logger = new Logger();
			$logger->log( 'ERROR: Request response is not an object. In web/app/themes/juniper-theme/inc/GoogleMaps/GoogleMapsConfig.php:' . __LINE__ );
			return false;
		}
		return $this->check_request_response( $google_maps_request_decode );
	}

	public function check_request_response( $google_maps_response ) {
		if ( 'OK' !== $google_maps_response->status ) {
			$logger = new Logger();
			$logger->log( 'ERROR: ' . $google_maps_response->status . ' ' . $google_maps_response->error_message . '. In web/app/themes/juniper-theme/inc/GoogleMaps/GoogleMapsConfig.php:' . __LINE__ );
			return false;
		}
		return $google_maps_response;
	}

	public function request_for_google_maps_data( $city ) {
		$response = $this->get_google_maps_coords( $city );
		if ( ! $response ) {
			return false;
		}
		return $response->results[0]->geometry->location->lat . ',' . $response->results[0]->geometry->location->lng;
	}

}
