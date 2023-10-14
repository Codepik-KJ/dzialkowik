<?php

namespace Dzialkowik\Taxonomies;

class RODTax {
	public string $taxonomy_slug;
	public string $taxonomy_name;

	public function __construct() {
		$this->taxonomy_slug = 'rod';
		$this->taxonomy_name = 'ROD';
	}

	public function register_custom_taxonomy() {
		$args = array(
			'label'        => $this->taxonomy_name,
			'public'       => true,
			'hierarchical' => true,
			'show_in_rest' => false,
		);

		register_taxonomy( $this->taxonomy_slug, array( 'plots' ), $args );

	}
	public function get_term_id( $rod_name ) {
		$get_term = get_term_by( 'name', $rod_name, 'rod' );
		if ( ! $get_term ) {
			return false;
		}
		return $get_term->term_id;
	}

}
