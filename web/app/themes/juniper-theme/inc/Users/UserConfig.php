<?php

namespace Dzialkowik\Users;

class UserConfig {

	public bool $has_dashboard_access;
	public string $user_role_slug;
	public string $user_role_display_name;
	public $current_user;

	public function __construct() {
		$this->current_user = new \WP_User( get_current_user_id() );
	}

	public function register_user_role( UserType $user_type ): void {
		$user_type->setup_user_role();
	}

}
