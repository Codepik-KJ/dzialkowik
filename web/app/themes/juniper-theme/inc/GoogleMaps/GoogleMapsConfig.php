<?php
namespace Dzialkowik\GoogleMaps;

use Dzialkowik\API\APIConfig;
use Dzialkowik\Logger\Logger;
use function Env\env;

class GoogleMapsConfig extends APIConfig {
	private $api_key;

	public function __construct() {
		$this->api_key = env( 'GOOGLE_API' );
	}

	public function get_google_maps_coords( $address ) {
		if ( ! $address ) {
			return false;
		}
		$google_maps_request_url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $address . '&key=' . $this->api_key;
		$google_maps_request     = wp_remote_request( $google_maps_request_url );
		return $this->check_request_response( $google_maps_request );
	}

	public function request_for_google_maps_data( $city ) {
		$response = $this->get_google_maps_coords( $city );
		if ( empty( $response->results[0]->geometry->location ) ) {
			$logger = new Logger();
			$logger->log( 'ERROR: error with getting coords In web/app/themes/juniper-theme/inc/GoogleMaps/GoogleMapsConfig.php:' . __LINE__ );
			return false;
		}

		return $response->results[0]->geometry->location->lat . ',' . $response->results[0]->geometry->location->lng;
	}

}
