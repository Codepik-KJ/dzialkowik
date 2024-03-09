<?php

namespace Dzialkowik\Cpt;

class ConfigCPT {

	public function check_post_link( $city, $rod_title, $post_id ) {
		if ( get_the_ID() !== $post_id ) {
			return;
		}
		if ( ! is_admin() ) {

			$url_parts = explode( '/', trim( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ), '/' ) );
			if ( empty( $url_parts[1] ) || empty( $url_parts[2] ) ) {
				return;
			}
			$url_city_slug = $url_parts[1];
			$url_rod_slug  = $url_parts[2];
			if ( $url_city_slug !== $city || $url_rod_slug !== $rod_title ) {
				global $wp_query;
				$wp_query->set_404();
				status_header( 404 );
				get_template_part( 404 );
				exit();
			}
		}
	}

}
