<?php

namespace Dzialkowik\Taxonomies;

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

		register_taxonomy( $this->taxonomy_slug, 'rod', $args );

	}

}
