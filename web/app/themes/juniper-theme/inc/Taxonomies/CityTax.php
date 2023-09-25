<?php

namespace Dzialkowik\Taxonomies;

use Dzialkowik\GoogleMaps\GoogleMapsConfig;
use Dzialkowik\OpenWeather\OWConfig;

class CityTax {
	public string $taxonomy_slug;
	public string $taxonomy_name;

	public function __construct() {
		$this->taxonomy_slug = 'city';
		$this->taxonomy_name = 'Miasta';
	}

	public function register_custom_taxonomy() {
		$args = array(
			'label'        => $this->taxonomy_name,
			'public'       => true,
			'hierarchical' => true,
			'show_in_rest' => false,
			'rewrite'      => array(
				'slug'       => 'miasto',
				'with_front' => true,
			),
			'meta_box_cb'  => false,
		);

		register_taxonomy( $this->taxonomy_slug, array( 'rod', 'plots' ), $args );

	}
	public function get_term_id( $city ) {
		$get_term = get_term_by( 'name', $city, 'city' );
		if ( ! $get_term ) {
			return false;
		}
		return $get_term->term_id;
	}
	public function update_city_coords( $city ) {
		$term_id = $this->get_term_id( $city );
		if ( ! $term_id ) {
			return;
		}
		$term_lat = get_term_meta( $term_id, 'lat', true );
		$term_lng = get_term_meta( $term_id, 'lng', true );

		if ( ! $term_lat && ! $term_lng ) {
			$google_maps_config   = new GoogleMapsConfig();
			$rod_coords_from_maps = $google_maps_config->get_google_maps_coords( $city );
			$response             = json_decode( $rod_coords_from_maps['body'] );
			$term_lat             = $response->results[0]->geometry->location->lat;
			$term_lng             = $response->results[0]->geometry->location->lng;

			update_term_meta( $term_id, 'lat', $term_lat );
			update_term_meta( $term_id, 'lng', $term_lng );
		}

	}

	public function get_city_coords( $term_id ) {
		return array(
			'lat' => get_term_meta( $term_id, 'lat', true ),
			'lng' => get_term_meta( $term_id, 'lng', true ),
		);
	}

	public function get_taxonomy_weather_data( $city ) {
		$term_id     = $this->get_term_id( $city );
		$ow_config   = new OWConfig();
		$city_coords = $this->get_city_coords( $term_id );

		if ( false === ( $get_open_weather_data = get_transient( 'get_open_weather_data' ) ) ) {
			// It wasn't there, so regenerate the data and save the transient
			$get_open_weather_data = $ow_config->get_open_weather_data( $city_coords['lat'], $city_coords['lng'] );
			set_transient( 'get_open_weather_data', $get_open_weather_data, 18 * HOUR_IN_SECONDS );
			update_term_meta( $term_id, 'city_weather', json_decode( $get_open_weather_data['body'] ) );
		}
		return get_term_meta( $term_id, 'city_weather', true );

	}

}
