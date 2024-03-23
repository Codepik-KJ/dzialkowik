<?php
namespace Dzialkowik\API;

use Dzialkowik\Logger\Logger;

class APIConfig {

	public function check_request_response( $response ) {
		if ( 200 !== $response['response']['code'] ) {
			$logger = new Logger();
			$logger->log( 'ERROR: Request response code is not 200. In web/app/themes/juniper-theme/inc/OpenWeather/OWConfig.php:' . __LINE__ );
			return false;
		}
		if ( ! isset( $response['body'] ) ) {
			$logger = new Logger();
			$logger->log( 'ERROR: Request response body is not set. In web/app/themes/juniper-theme/inc/API/APIConfig.php:' . __LINE__ );
			return false;
		}
		return $this->check_request_decode( json_decode( $response['body'] ) );
	}

	public function check_request_decode( $response_decoded_body ) {
		if ( ! is_object( $response_decoded_body ) ) {
			$logger = new Logger();
			$logger->log( 'ERROR: Request response is not an object. In web/app/themes/juniper-theme/inc/API/APIConfig.php:' . __LINE__ );
			return false;
		}
		if ( isset( $response_decoded_body->error_message ) ) {
			$logger = new Logger();
			$logger->log( 'ERROR: ' . $response_decoded_body->error_message . ' In web/app/themes/juniper-theme/inc/API/APIConfig.php:' . __LINE__ );
			return false;
		}
		return $response_decoded_body;
	}
}
