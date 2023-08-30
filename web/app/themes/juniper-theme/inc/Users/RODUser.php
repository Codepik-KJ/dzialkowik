<?php

namespace Dzialkowik\Users;

class RODUser extends UserType {

	public function __construct() {
		$this->set_dashboard_access();
	}

	public function set_user_role_slug() {
		$this->user_role_slug = 'rod_user';
	}
	public function set_user_role_display_name() {
		$this->user_role_display_name = 'Użytkownik ROD';
	}
	public function set_dashboard_access() {
		$this->has_dashboard_access = true;
	}

	public function add_user_role() {
		add_role( $this->user_role_slug, $this->user_role_display_name, get_role( 'subscriber' )->capabilities );
	}

	public function add_RODCPT_caps() {
		$roles = array( get_role( 'administrator' ), get_role( 'rod_user' ) );
		foreach ( $roles as $role ) {
			$role->add_cap( 'edit_rod' );
			$role->add_cap( 'edit_rods' );
			$role->add_cap( 'edit_others_rods' );
			$role->add_cap( 'publish_rods' );
			$role->add_cap( 'read_rod' );
			$role->add_cap( 'read_private_rods' );
			$role->add_cap( 'delete_rod' );
		}
	}

}
