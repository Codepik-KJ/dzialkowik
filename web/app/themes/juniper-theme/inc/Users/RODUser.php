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

	public function hide_other_roles( $user_query ) {

		$current_user = wp_get_current_user();
		if ( in_array( 'administrator', $current_user->roles, true ) === false ) {
			global $wpdb;
			$user_query->query_where = str_replace(
				'WHERE 1=1',
				"WHERE 1=1 AND {$wpdb->users}.ID IN (
                SELECT {$wpdb->usermeta}.user_id FROM $wpdb->usermeta 
                    WHERE {$wpdb->usermeta}.meta_key = '{$wpdb->prefix}capabilities'
                    AND {$wpdb->usermeta}.meta_value LIKE '%rod_user%' OR {$wpdb->usermeta}.meta_value LIKE '%plot_user%')",
				$user_query->query_where
			);
		}
	}

	public function prevent_to_set_specific_role( $all_roles ) {
		$current_user = wp_get_current_user();
		if ( in_array( 'administrator', $current_user->roles, true ) === false ) {
			$available_roles = array( 'plot_user', 'rod_user' );
			foreach ( $all_roles as $key => $role ) {

				if ( in_array( $key, $available_roles, true ) === false ) {
					unset( $all_roles[ $key ] );
				}
			}
		}

		return $all_roles;
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
			$role->add_cap( 'edit_plot' );
			$role->add_cap( 'edit_plots' );
			$role->add_cap( 'edit_others_plots' );
			$role->add_cap( 'publish_plots' );
			$role->add_cap( 'read_plot' );
			$role->add_cap( 'read_private_plots' );
			$role->add_cap( 'delete_plot' );
			$role->add_cap( 'list_users' );
			$role->add_cap( 'create_users' );
			$role->add_cap( 'promote_users' );
		}
	}

}
