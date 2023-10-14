<?php
namespace Dzialkowik\Forms;

class FormConfig {

	protected $form_slug;
	protected $post_type;
	protected $post_id;

	public function __construct( $form_slug, $post_type, $post_id ) {
		$this->form_slug = $form_slug;
		$this->post_type = $post_type;
		$this->post_id   = $post_id;
	}

	public function register_form() : void {
		if ( function_exists( 'acf_register_form' ) ) {

			// Register form.
			acf_register_form(
				array(
					'id'           => $this->form_slug,
					'post_id'      => $this->post_id,
					'post_title'   => false,
					'post_content' => false,
					'fields'       => array( 'opis_dzialki' ),
					'form'         => true,
				)
			);
		}
	}

}
