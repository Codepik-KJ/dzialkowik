<?php

namespace Dzialkowik\Cpt;

class RODCPT {
	public string $cpt_slug;
	public string $cpt_name;

	public function __construct() {
		$this->cpt_slug = 'rod';
		$this->cpt_name = 'ROD';

		add_action( 'init', array( $this, 'register_custom_cpt' ) );
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
			)
		);
	}
}
