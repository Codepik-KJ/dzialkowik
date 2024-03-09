<?php

namespace Dzialkowik\Cpt;

class EventsCPT {
	public string $cpt_slug;
	public string $cpt_name;

	public function __construct() {
		$this->cpt_slug = 'events';
		$this->cpt_name = 'Wydarzenia';

	}

	public function register_custom_cpt() {
		register_post_type(
			$this->cpt_slug,
			array(
				'labels'       => array(
					'name'          => $this->cpt_name,
					'singular_name' => $this->cpt_name,
				),
				'public'       => true,
				'has_archive'  => true,
				'show_in_rest' => true,
				'supports'     => array( 'title' ),
				'capabilities' => array(
					'edit_post'          => 'edit_event',
					'edit_posts'         => 'edit_events',
					'edit_others_posts'  => 'edit_others_events',
					'publish_posts'      => 'publish_events',
					'read_post'          => 'read_event',
					'read_private_posts' => 'read_private_events',
					'delete_post'        => 'delete_event',
				),
				'rewrite'      => array( 'slug' => 'wydarzenia' ),
			)
		);
	}

	public function events_admin_fields( $field ) {
		if ( ! current_user_can( 'administrator' ) ) {
			return;
		}
		if ( function_exists( 'acf_add_local_field_group' ) ) :
			acf_add_local_field_group(
				array(
					'key'                   => 'events_admin_group',
					'title'                 => 'Events admin group',
					'fields'                => array(
						array(
							'key'               => 'field_65e61a477eaeb',
							'label'             => 'Is global',
							'name'              => 'is_global',
							'aria-label'        => '',
							'type'              => 'true_false',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'message'           => '',
							'default_value'     => 0,
							'ui'                => 0,
							'ui_on_text'        => '',
							'ui_off_text'       => '',
						),
					),
					'location'              => array(
						array(
							array(
								'param'    => 'post_type',
								'operator' => '==',
								'value'    => 'events',
							),
						),
					),
					'menu_order'            => 0,
					'position'              => 'normal',
					'style'                 => 'default',
					'label_placement'       => 'top',
					'instruction_placement' => 'label',
					'hide_on_screen'        => '',
				)
			);

		endif;
	}

	public function query_events( $rod_id, $meta_key = 'rod' ): array {
		if ( empty( $rod_id ) ) {
			return array();
		}
		$date_current = $this->get_selected_date( gmdate( 'Y-m-d', time() ), 'Y-m-d' );
		$events       = array(
			'post_type'      => 'events',
			'posts_per_page' => -1,
			'orderby'        => 'meta_value',
			'meta_key'       => 'date_start',
			'order'          => 'ASC',
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'relation' => 'OR', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					array(
						'key'     => $meta_key,
						'value'   => $rod_id,
						'compare' => 'LIKE',
					),
					array(
						'key'     => 'is_global',
						'value'   => 1,
						'compare' => '=',
					),
				),
				array(
					'relation' => 'OR',
					array(
						'key'     => 'date_start',
						'value'   => $date_current,
						'compare' => '>=',
						'type'    => 'DATE',
					),
					array(
						'key'     => 'date_end',
						'value'   => $date_current,
						'compare' => '>=',
						'type'    => 'DATE',
					),
				),

			),
		);
		$events_query = new \WP_Query( $events );
		return $this->build_events( $events_query->get_posts() );
	}

	public function get_selected_date( $date, $format = 'd-m-Y' ) {
		$timestamp = strtotime( str_replace( '/', '-', $date ) );
		return wp_date( $format, $timestamp );
	}

	public function date_same_month( $date, $date_start_formatted, $date_end_formatted ) {
		$month = $this->get_selected_date( $date_start_formatted, 'F' );
		if ( $this->get_selected_date( $date_end_formatted, 'F' ) === $month ) {
			return $month . ' ' . $this->get_selected_date( $date_start_formatted, 'd' ) . ' - ' . $this->get_selected_date( $date_end_formatted, 'd' ) . ', ' . $this->get_selected_date( $date_start_formatted, 'Y' );
		}

		return $date;
	}

	public function build_events( $events_query ): array {

		$events_array = array();
		foreach ( $events_query as $event ) {
			$event_id   = $event->ID;
			$date_start = get_field( 'date_start', $event_id );
			$date_end   = get_field( 'date_end', $event_id );
			$date       = '';

			if ( $date_start ) {
				$date_start_formatted = $this->get_selected_date( $date_start, 'F d, Y' );
				$date                .= $date_start_formatted;
			}
			if ( $date_end ) {
				$date_end_formatted = $this->get_selected_date( $date_end, 'F d, Y' );
				$date              .= ' - ' . $date_end_formatted;
			}
			$event_array    = array(
				'ID'    => $event_id,
				'date'  => $date,
				'link'  => get_permalink( $event_id ),
				'title' => get_the_title( $event_id ),
			);
			$events_array[] = $event_array;
		}
		return $events_array;
	}

	public function get_all_available_events() {
		$date_current = $this->get_selected_date( gmdate( 'Y-m-d', time() ), 'Y-m-d' );
		$events = array(
			'post_type'      => 'events',
			'posts_per_page' => -1,
			'orderby'        => 'meta_value',
			'meta_key'       => 'date_start',
			'order'          => 'ASC',
			'meta_query'     => array(
				array(
					'relation' => 'OR',
					array(
						'key'     => 'date_start',
						'value'   => $date_current,
						'compare' => '>=',
						'type'    => 'DATE',
					),
					array(
						'key'     => 'date_end',
						'value'   => $date_current,
						'compare' => '>=',
						'type'    => 'DATE',
					),
				),
			),

		);
		$events_query = new \WP_Query( $events );
		return $this->build_events( $events_query->get_posts() );
	}
}

