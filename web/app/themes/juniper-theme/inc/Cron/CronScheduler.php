<?php

namespace Dzialkowik\Cron;

class CronScheduler {

	private $hook_name;

	public function __construct( string $hook_name ) {
		$this->hook_name = $hook_name;
	}

	public function get_event(): string {
		return $this->hook_name;
	}

	public function schedule_event() {
		$hook_name = $this->get_event();
		$timestamp = wp_next_scheduled( $hook_name );
		if ( $timestamp === false ) {
			wp_schedule_event( time(), 'hourly', $hook_name );
		}
	}

	public function unschedule_event() {
		$hook_name = $this->get_event();
		$timestamp = wp_next_scheduled( $hook_name );
		if ( $timestamp !== false ) {
			wp_unschedule_event( $timestamp, $hook_name );
		}
	}
}