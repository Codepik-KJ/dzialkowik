<?php

namespace Dzialkowik\Users;

class UserConfig {

	public bool $has_dashboard_access;
	public string $user_role_slug;
	public string $user_role_display_name;
	public \WP_User $current_user;
	public int $current_user_id;

	public function __construct() {
		$this->current_user_id = get_current_user_id();
		$this->current_user    = new \WP_User( $this->current_user_id );
	}

	public function register_user_role( UserType $user_type ): void {
		$user_type->setup_user_role();
	}

	public function get_rods_assigned_to_user( $user_id ) {
		return get_user_meta( $user_id, 'rod_assigned', true );
	}

	public function get_rod_meta_for_user( $user_id ) {
		$rod_assigned = get_user_meta( $user_id, 'rod_assigned', true );
		if ( ! is_array( $rod_assigned ) ) {
			update_user_meta( $user_id, 'rod_assigned', array() );
		}
		return $rod_assigned;
	}

}
