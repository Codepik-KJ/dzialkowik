<?php

namespace Dzialkowik\Users;

class PlotUser extends UserType {

	public function __construct() {
		$this->set_dashboard_access();
	}

	public function set_user_role_slug() {
		$this->user_role_slug = 'plot_user';
	}
	public function set_user_role_display_name() {
		$this->user_role_display_name = 'Działkowiec';
	}
	public function set_dashboard_access() {
		$this->has_dashboard_access = false;
	}

	public function add_user_role() {
		add_role( $this->user_role_slug, $this->user_role_display_name, get_role( 'subscriber' )->capabilities );
	}

}
