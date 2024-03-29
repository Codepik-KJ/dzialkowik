<?php

namespace Dzialkowik\Users;

abstract class UserType extends UserConfig {

	abstract public function set_user_role_slug();
	abstract public function set_user_role_display_name();
	abstract public function set_dashboard_access();
	abstract public function add_user_role();
	abstract public function show_user_specific_content( $wp_query_obj );
	abstract public function is_user_assigned_to_rod( $rod_id, $user_id );

	public function setup_user_role() {
		$this->set_user_role_slug();
		$this->set_user_role_display_name();
		$this->set_dashboard_access();
		$this->add_user_role();
		$this->prevent_dashboard_access();
	}

	public function prevent_dashboard_access() : void {
		if ( current_user_can( $this->user_role_slug ) ) {
			show_admin_bar( $this->has_dashboard_access );
			if ( false === $this->has_dashboard_access ) {
				if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
					wp_redirect( home_url() );
					exit;
				}
			}
		}
	}
}
