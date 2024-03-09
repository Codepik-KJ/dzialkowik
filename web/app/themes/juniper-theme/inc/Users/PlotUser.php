<?php

namespace Dzialkowik\Users;

class PlotUser extends UserType {

	public int $current_user_id;


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

	public function set_current_user_id() {
		$this->current_user_id = get_current_user_id();
	}

	public function is_plot_user() {

		$user = new \WP_User( $this->current_user_id );
		if ( in_array( $this->user_role_slug, $user->roles, true ) ) {
			return true;
		}
		return false;

	}

	public function is_user_allowed_to_edit_plot( $plot_id ) {
		if ( ! $this->is_plot_user() ) {
			return false;
		}
		$plot_owner = get_field( 'plot_owner', $plot_id );
		if ( $this->current_user_id === $plot_owner ) {
			return true;
		}
		return false;
	}

	public function add_PLOT_caps() {
		$roles = array( get_role( 'plot_user' ) );
		foreach ( $roles as $role ) {
			$role->add_cap( 'edit_plot' );
			$role->add_cap( 'edit_plots' );
			$role->add_cap( 'edit_others_plots' );
			$role->add_cap( 'publish_plots' );
			$role->add_cap( 'read_plot' );
			$role->add_cap( 'read_private_plots' );
		}
	}

	public function show_user_specific_content( $wp_query_obj ) {
		if ( ! is_admin() ) {
			return;
		}

		global $current_user;

		if ( ! is_a( $current_user, 'WP_User' ) ) {
			return;
		}
		if ( $this->is_plot_user() ) {
			$wp_query_obj->set( 'author', $this->current_user_id );
		}
	}

	public function is_user_assigned_to_rod( $rod_id, $user_id ) {
		$get_user_plots_assigned = get_user_meta( $user_id, 'plot_assigned', true );
		if ( empty( $get_user_plots_assigned ) ) {
			return false;
		}
		foreach ( $get_user_plots_assigned as $plot ) {
			$plot_rod = get_field( 'rod', $plot );
			if ( $plot_rod === $rod_id ) {
				return true;
			}
		}
		return false;
	}

}
