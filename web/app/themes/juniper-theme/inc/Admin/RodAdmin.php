<?php

namespace Dzialkowik\Admin;

use Dzialkowik\Users\UserConfig;

class RodAdmin {

	public function pre_get_posts_for_rod( $wp_query_obj, $current_user ) {
		if ( is_admin() ) {

			global $wpdb;

			$author_posts_ids = $this->get_author_posts( $wpdb, $current_user );

			$rod_assigned_to_user = get_user_meta( $current_user->ID, 'rod_assigned', true );
			$post_in_results      = $this->get_post_in_posts( $wpdb, $rod_assigned_to_user );
			$merged               = array_merge( $author_posts_ids, $post_in_results );

			$get_events_assigned_to_user_rod = $this->get_events_assigned_to_user_rod( $wpdb, $rod_assigned_to_user );

			$rods  = array_unique( $merged );
			$plots = $this->get_all_plots_assigned_to_rods( $wpdb, $rods );

			$items      = array_merge( $rods, $plots, $get_events_assigned_to_user_rod );
			$rods_plots = array_unique( $items );
			$wp_query_obj->set( 'post__in', $items );
		}
	}

	public function get_author_posts( $wpdb, $current_user ) {
		$query_author_posts = $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_author = %d", $current_user->ID );
		return $wpdb->get_col( $query_author_posts );
	}

	public function get_post_in_posts( $wpdb, $array_with_posts ) {
		if ( is_array( $array_with_posts ) ) {
			$placeholders  = array_fill( 0, count( $array_with_posts ), '%d' );
			$format        = implode( ', ', $placeholders );
			$query_post_in = $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE ID IN ($format)", $array_with_posts );
			return $wpdb->get_col( $query_post_in );
		}
		return array();
	}

	public function get_all_plots_assigned_to_rods( $wpdb, $rods ) {
		$plots = array();
		foreach ( $rods as $rod ) {
			$query   = $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'rod' AND meta_value = %d", $rod );
			$results = $wpdb->get_col( $query );
			$plots   = array_merge( $plots, $results );
		}
		return $plots;
	}

	public function get_events_assigned_to_user_rod( $wpdb, $rod_assigned_to_user ) {
		if ( ! is_array( $rod_assigned_to_user ) || empty( $rod_assigned_to_user ) ) {
			return array();
		}
		$rod_assigned_to_user = array_map( 'intval', $rod_assigned_to_user );
		$regex_pattern        = implode( '|', $rod_assigned_to_user );

		$query = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'rod' AND CAST(meta_value AS CHAR) REGEXP %s",
				$regex_pattern
			),
			ARRAY_A
		);

		return wp_list_pluck( $query, 'post_id' );
	}

}


