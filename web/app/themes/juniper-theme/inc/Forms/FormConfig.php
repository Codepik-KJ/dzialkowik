<?php
namespace Dzialkowik\Forms;

class FormConfig {

	protected $form_slug;
	protected $form_fields_id;
	protected $post_type;

	public function __construct( $form_slug, $form_fields_id, $post_type ) {
		$this->form_slug      = $form_slug;
		$this->form_fields_id = $form_fields_id;
		$this->post_type      = $post_type;
	}

	public function register_form() : void {
		if ( function_exists( 'acf_register_form' ) ) {

			// Register form.
			acf_register_form(
				array(
					'id'           => $this->form_slug,
					'post_id'      => 'new_post',
					'new_post'     => array(
						'post_type'   => $this->post_type,
						'post_status' => 'publish',
					),
					'field_groups' => array( $this->form_fields_id ),
					'post_title'   => false,
					'post_content' => false,
				)
			);
		}
	}

}
