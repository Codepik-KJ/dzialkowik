<?php

namespace Dzialkowik\Cpt;

class RODCPT {
	public string $cpt_slug;
	public string $cpt_name;

	public function __construct() {
		$this->cpt_slug = 'rod';
		$this->cpt_name = 'ROD';

		add_action( 'init', array( $this, 'register_custom_cpt' ) );
		add_filter( 'post_type_link', array( $this, 'change_rod_title_as_city_tax' ), 10, 2 );
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
				'has_archive'  => false,
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
					'slug'       => '%city%/rod',
					'with_front' => false,
				),
			)
		);
		add_rewrite_rule(
			'(.*)/rod/(.*)',
			'index.php?post_type=rod&name=$matches[2]',
			'top'
		);
	}

	public function change_rod_title_as_city_tax( $link, $post_id ) {

		if ( get_post_type( $post_id ) === 'rod' ) {
			$link = str_replace( '%city%', $this->get_rod_city_slug( $post_id ), $link );
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
}
