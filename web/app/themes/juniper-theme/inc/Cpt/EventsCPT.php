<?php

namespace Dzialkowik\Cpt;

class EventsCPT {
	public string $cpt_slug;
	public string $cpt_name;

	public function __construct() {
		$this->cpt_slug = 'events';
		$this->cpt_name = 'Wydarzenia';

		//      TODO tutaj masz w construcie register_custom_cpt wywołany -> nie powinno go tutaj być (w construcie nie wrzucamy takich rzeczy). Masz je wszystkie w include.php.
		//      TODO Tego jednego akurat brakuje -> więc wydarzenia się nie ładują.

		//      add_action( 'init', array( $this, 'register_custom_cpt' ) );
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
				'supports'     => array( 'title', 'editor' ),
				'rewrite'      => array( 'slug' => $this->cpt_slug ),
			)
		);
	}
}
