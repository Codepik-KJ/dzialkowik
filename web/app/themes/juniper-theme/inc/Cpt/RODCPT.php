<?php

namespace Dzialkowik\Cpt;

use Dzialkowik\Admin\RodAdmin;
use Dzialkowik\Taxonomies\CityTax;
use Dzialkowik\Users\RODUser;

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
					'slug'       => '%city%',
					'with_front' => false,
				),
			)
		);
		add_rewrite_tag( '%city%', '([^&]+)' );
		add_rewrite_rule( '^([^/]*)/([^/]*)/?$', 'index.php?post_type=rod&city=$matches[1]&name=$matches[2]', 'top' );
	}

	public function change_rod_link_to_match_city_tax( $link, $post_id ) {
		if ( get_post_type( $post_id ) === 'rod' ) {
			$city_slug     = $this->get_rod_city_slug( $post_id );
			$rod_slug      = get_post_field( 'post_name', $post_id );
			$link          = str_replace( '%city%', $city_slug, $link );
			$url_parts     = explode( '/', trim( parse_url( $link, PHP_URL_PATH ), '/' ) );
			$url_city_slug = $url_parts[0];
			$url_rod_slug  = $url_parts[1];
			//TODO obecnie kazdy url działa np http://dzialkowik.local/zgorzelec/zabobrze/ http://dzialkowik.local/zsaddasdasdasec/zabobrze/
			if ( $url_city_slug !== $city_slug || $url_rod_slug !== $rod_slug ) {
				global $wp_query;
				$wp_query->set_404();
				status_header( 404 );
				get_template_part( 404 );
				exit();
			}
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
