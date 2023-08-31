<?php

namespace Dzialkowik\Taxonomies;

class CityTax {
	public string $taxonomy_slug;
	public string $taxonomy_name;

	public function __construct() {
		$this->taxonomy_slug = 'city';
		$this->taxonomy_name = 'Miasta';

		add_action( 'init', array( $this, 'register_custom_taxonomy' ) );
	}

	public function register_custom_taxonomy() {
		$args = array(
			'label'        => $this->taxonomy_name,
			'public'       => true,
			'hierarchical' => true,
			'show_in_rest' => false,
			'meta_box_cb'  => false,
		);

		register_taxonomy( $this->taxonomy_slug, 'rod', $args );
	}

}
