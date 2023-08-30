<?php

namespace Dzialkowik\Cpt;

class PlotsCPT {
	public string $cpt_slug;
	public string $cpt_name;

	public function __construct() {
		$this->cpt_slug = 'plots';
		$this->cpt_name = 'Działka';

		add_action( 'init', array( $this, 'register_custom_cpt' ) );
		add_action( 'acf/save_post', array( $this, 'set_plot_title' ) );
		add_filter( 'post_type_link', array( $this, 'post_type_as_link' ), 10, 2 );

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
				'supports'     => array( 'title', 'editor' ),
				'capabilities' => array(
					'edit_post'          => 'edit_rod',
					'edit_posts'         => 'edit_rods',
					'edit_others_posts'  => 'edit_others_rods',
					'publish_posts'      => 'publish_rods',
					'read_post'          => 'read_rod',
					'read_private_posts' => 'read_private_rods',
					'delete_post'        => 'delete_rod',
				),
				'map_meta_cap' => true,
				'rewrite'      => array(
					'slug'       => 'rod/%rod_name%',
					'with_front' => false,
				),
			)
		);
		add_rewrite_rule(
			'rod/(.*)/(.*)',
			'index.php?post_type=plots&name=$matches[2]',
			'top'
		);
	}

	public function is_plots_cpt( $post_id ) {
		$post_type = get_post_type( $post_id );
		if ( $post_type !== 'plots' ) {
			return false;
		}
		return true;
	}

	public function set_plot_title( $post_id ) {
		if ( ! $this->is_plots_cpt( $post_id ) ) {
			return;
		}
		$rod         = get_field( 'rod', $post_id );
		$rod_title   = get_field( 'rod_title', $rod->ID );
		$plot_number = get_field( 'numer', $post_id );
		$post_update = array(
			'ID'         => $post_id,
			'post_title' => $rod_title . '/' . $plot_number,
			'post_name'  => strtolower( $rod_title ) . '/' . $plot_number,
		);

		wp_update_post( $post_update );
	}

	public function post_type_as_link( $link, $post_id ) {
		$rod = get_field( 'rod', $post_id );
		if ( get_post_type( $post_id ) === 'plots' && $rod ) {
			$rod_title = strtolower( get_field( 'rod_title', $rod->ID ) );
			$link      = str_replace( '%rod_name%', $rod_title, $link );
		}
		return $link;
	}


}
