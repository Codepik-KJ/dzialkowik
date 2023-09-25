<?php
namespace Dzialkowik\GoogleMaps;

class GoogleMapsConfig {
	private $api_key;

	public function __construct() {
		$this->api_key = 'AIzaSyD5tEDAOafGdgUjtB7pwP29zHeyuZb-zYE';
	}

	public function get_google_maps_coords( $address ) {
		if ( ! $address ) {
			return false;
		}
		$url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $address . '&key=' . $this->api_key;
		return wp_remote_request( $url );
	}

}
