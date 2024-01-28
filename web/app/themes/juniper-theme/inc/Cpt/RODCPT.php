<?php

namespace Dzialkowik\Cpt;

use Dzialkowik\Taxonomies\CityTax;

class RODCPT {
	public string $cpt_slug;
	public string $cpt_name;

	public function __construct() {
		$this->cpt_slug = 'rod';
		$this->cpt_name = 'ROD';
	}

	public function register_custom_cpt() {
		register_post_type(
			$this->cpt_slug,
			array(
				'labels'       => array(
					'name'          => $this->cpt_name,
					'singular_name' => $this->cpt_name,
				),
				'public'       => true,
				'has_archive'  => true,
				'show_in_rest' => true,
				'supports'     => array( 'title' ),
				'taxonomies'   => array( 'city' ),
				'capabilities' => array(
					'edit_post'          => 'edit_rod',
					'edit_posts'         => 'edit_rods',
					'edit_others_posts'  => 'edit_others_rods',
					'publish_posts'      => 'publish_rods',
					'read_post'          => 'read_rod',
					'read_private_posts' => 'read_private_rods',
					'delete_post'        => 'delete_rod',
				),
				'rewrite'      => array(
					'slug'       => 'rod/%rod_city_name%',
					'with_front' => false,
				),
			)
		);
		add_rewrite_tag( '%rod_city_name%', '([^&]+)', 'rod_city_name=' );
		add_rewrite_rule(
			'^rod/([^/]*)/([^/]*)/?$',
			'index.php?post_type=rod&rod_city_name=$matches[1]&name=$matches[2]',
			'top'
		);
	}

	public function change_rod_title_as_city_tax( $link, $post_id ) {

		if ( get_post_type( $post_id ) === 'rod' ) {
			$link = str_replace( '%rod_city_name%', $this->get_rod_city_slug( $post_id ), $link );
		}
		return $link;
	}

	public function get_rod_city_slug( $post_id ) {
		$get_post_terms = get_the_terms( $post_id, 'city' );
		$city_name      = 'miasto';
		if ( $get_post_terms ) {
			$city_name = $get_post_terms[0]->slug;
		}
		return $city_name;
	}

	public function is_rod_cpt( $post_id ) {
		$post_type = get_post_type( $post_id );
		if ( 'rod' !== $post_type ) {
			return false;
		}
		return true;
	}

	public function set_rod_city_tax( $post_id ) {
		if ( ! $this->is_rod_cpt( $post_id ) ) {
			return;
		}
		$city     = get_post_meta( $post_id, 'city', true );
		$city_tax = new CityTax();
		$city_tax->update_city_coords( $city );
		wp_set_object_terms( $post_id, array( $city ), 'city' );
	}

}
