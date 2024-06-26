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

	public function get_user_if_assigned_to_rod( UserType $user_type, $user_id, $rod_id ) {
		if ( $user_type->is_user_assigned_to_rod( $rod_id, $user_id ) ) {
			return $user_id;
		}
		return false;
	}

	public function get_all_rod_users( $rod_id ) {
		$users                 = get_users();
		$users_assigned_to_rod = array();
		foreach ( $users as $user ) {

			if ( 'rod_user' === $user->roles[0] ) {
				$user_id_if_assigned = $this->get_user_if_assigned_to_rod( new RODUser(), $user->ID, $rod_id );
				if ( ! empty( $user_id_if_assigned ) ) {
					$users_assigned_to_rod[] = $user_id_if_assigned;
				}
			}
			if ( 'plot_user' === $user->roles[0] ) {
				$user_id_if_assigned = $this->get_user_if_assigned_to_rod( new PlotUser(), $user->ID, $rod_id );
				if ( ! empty( $user_id_if_assigned ) ) {
					$users_assigned_to_rod[] = $user_id_if_assigned;
				}
			}
		}

		return $users_assigned_to_rod;
	}

	public function get_all_users() {
		$users     = get_users();
		$all_users = array();
		foreach ( $users as $user ) {
			$all_users[] = $user->ID;
		}
		return $all_users;
	}

	public function count_users_with_role( $role ): ?int {
		$current_user_id = get_current_user_id();
		$args            = array(
			'role'       => $role,
			'meta_key'   => 'created_by_user_id',
			'meta_value' => $current_user_id,

		);
		$users = get_users( $args );
		return count( $users );
	}

	public function modify_pre_count_users_defaults( $result, $strategy, $site_id ) {
		if ( current_user_can( 'administrator' ) ) {
			return $result;
		}
		$available_roles = array(
			'plot_user',
			'rod_user',
		);
		foreach ( $available_roles as $role ) {
			$result['avail_roles'][ $role ] = $this->count_users_with_role( $role );
		}
		$result['total_users'] = $result['avail_roles']['plot_user'] + $result['avail_roles']['rod_user'];

		return $result;
	}

}
