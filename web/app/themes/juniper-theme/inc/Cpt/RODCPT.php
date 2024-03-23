<?php

namespace Dzialkowik\Cpt;

use Dzialkowik\Admin\RodAdmin;
use Dzialkowik\Taxonomies\CityTax;
use Dzialkowik\Users\RODUser;

class RODCPT extends ConfigCPT {
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
					'slug'       => 'rod/%city%',
					'with_front' => false,
				),
			)
		);
		add_rewrite_tag( '%city%', '([^&]+)' );
		add_rewrite_rule( '^rod/([^/]*)/([^/]*)/?$', 'index.php?post_type=rod&city=$matches[1]&name=$matches[2]', 'top' );
	}

	public function change_rod_link_to_match_city_tax( $link, $post ) {
		$post_id = $post->ID;
		if ( get_post_type( $post_id ) === 'rod' ) {
			$city_slug = $this->get_rod_city_slug( $post_id );
			$rod_slug  = get_post_field( 'post_name', $post_id );
			$link      = str_replace( '%city%', $city_slug, $link );

			$this->check_post_link( $city_slug, $rod_slug, $post_id );
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
		wp_set_object_terms( $post_id, array( $city ), 'city' );
		$city_tax->update_city_coords( $city );
	}

	public function query_rods( $post_id ) {
		$events       = array(
			'post_type'      => 'events',
			'posts_per_page' => -1,
			'meta_query'     => array(
				'relation' => 'OR',
				array(
					'key'     => 'rod',
					'value'   => $post_id,
					'compare' => 'LIKE',
				),
				array(
					'key'     => 'is_global',
					'value'   => 1,
					'compare' => '=',
				),
			),
		);
		$events_query = new \WP_Query( $events );
		return $events_query->get_posts();
	}

}
