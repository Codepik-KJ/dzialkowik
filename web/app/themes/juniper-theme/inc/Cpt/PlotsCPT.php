<?php

namespace Dzialkowik\Cpt;

use Dzialkowik\Admin\RodAdmin;
use Dzialkowik\Users\RODUser;

class PlotsCPT {
	public string $cpt_slug;
	public string $cpt_name;

	public function __construct() {
		$this->cpt_slug = 'plots';
		$this->cpt_name = 'Działka';

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
				'taxonomies'   => array( 'city', 'rod_tax' ),
				'supports'     => array( 'title' ),
				'capabilities' => array(
					'edit_post'          => 'edit_plot',
					'edit_posts'         => 'edit_plots',
					'edit_others_posts'  => 'edit_others_plots',
					'publish_posts'      => 'publish_plots',
					'read_post'          => 'read_plot',
					'read_private_posts' => 'read_private_plots',
					'delete_post'        => 'delete_plot',
				),
				'rewrite'      => array(
					'slug'       => 'rod/%city_name%/%rod_name%',
					'with_front' => false,
				),
			)
		);
		add_rewrite_tag( '%city_name%', '([^&]+)', 'city_name=' );
		add_rewrite_tag( '%rod_name%', '([^&]+)', 'rod_name=' );
		add_rewrite_rule(
			'^rod/([^/]*)/([^/]*)/([^/]*)/?$',
			'index.php?post_type=plots&city_name=$matches[1]&rod_name=$matches[2]&name=$matches[3]',
			'top'
		);
	}

	public function is_plots_cpt( $post_id ) {
		$post_type = get_post_type( $post_id );
		if ( 'plots' !== $post_type ) {
			return false;
		}
		return true;
	}
	/*
	 * On acf/save_post hook, update plot title and name with rod and city taxonomies
	 */
	public function update_plot_data( $post_id ) {
		if ( ! $this->is_plots_cpt( $post_id ) ) {
			return;
		}
		$rod         = get_field( 'rod', $post_id );
		$rod_title   = get_field( 'rod_title', $rod );
		$rod_term    = get_field( 'city', $rod );
		$plot_number = get_field( 'numer', $post_id );
		$post_update = array(
			'ID'         => $post_id,
			'post_title' => $rod_title . '/' . $plot_number,
			'post_name'  => strtolower( $rod_title ) . '/' . $plot_number,
		);
		wp_set_object_terms( $post_id, array( $rod_term ), 'city' );
		wp_set_object_terms( $post_id, array( $rod_title ), 'rod_tax' );
		wp_update_post( $post_update );
		wp_update_post( $post_update );
	}

	public function post_type_as_link( $link, $post_id ) {
		$rod = get_field( 'rod', $post_id );
		if ( get_post_type( $post_id ) === 'plots' && $rod ) {
			$dzialkowik_rod_cpt = new RODCPT();
			$city               = $dzialkowik_rod_cpt->get_rod_city_slug( $rod );
			$rod_title          = strtolower( get_field( 'rod_title', $rod ) );
			$link               = str_replace( '%rod_name%', $rod_title, $link );
			$link               = str_replace( '%city_name%', $city, $link );
		}
		return $link;
	}
}
