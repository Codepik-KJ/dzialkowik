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
		$term_lat_lng = get_term_meta( $term_id, 'lat_lng', true );

		if ( ! $term_lat_lng ) {
			$google_maps_config   = new GoogleMapsConfig();
			$term_lat_lng = $google_maps_config->request_for_google_maps_data( $city );
			if ( ! $term_lat_lng ) {
				return;
			}
			update_term_meta( $term_id, 'lat_lng', $term_lat_lng );
		}

	}

	public function get_city_coords( $term_id ) {
		$exploded_string = explode( ',', get_term_meta( $term_id, 'lat_lng', true ) );
		if ( ! $exploded_string ) {
			return false;
		}
		return array(
			'lat' => $exploded_string[0],
			'lng' => $exploded_string[1],
		);
	}

	public function get_taxonomy_weather_data( $city ) {
		$term_id   = $this->get_term_id( $city );
		$ow_config = new OWConfig();
		return $ow_config->get_cached_weather_data( $term_id );
	}

}
