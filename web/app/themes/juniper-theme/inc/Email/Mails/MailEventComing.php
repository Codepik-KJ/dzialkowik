<?php

namespace Dzialkowik\Email;

use Dzialkowik\Cpt\EventsCPT;
use Dzialkowik\Logger\Logger;
use Dzialkowik\Users\UserConfig;
use Dzialkowik\Users\UserType;

class MailEventComing extends EmailConfig {

	private $event_cpt;

	public function __construct() {
		$this->event_cpt = new EventsCPT();
	}

	public function get_event_email_message( $event_id ) {

		$message = get_field( 'email', $event_id );

		if ( empty( $message ) ) {
			$message = false;
		}

		return $message;
	}

	public function check_if_is_global( $event_id ): bool {
		$is_global = get_field( 'is_global', $event_id );
		if ( empty( $is_global ) ) {
			return false;
		}
		return true;
	}

	public function check_if_send_email( $event_id ): bool {
		$send_email = get_field( 'send_email_on_event_start', $event_id );
		if ( empty( $send_email ) ) {
			return false;
		}
		return true;
	}

	public function check_if_email_already_sent( $event_id ): bool {
		$send_email = get_field( 'email_was_sent', $event_id );
		if ( empty( $send_email ) ) {
			return false;
		}
		return true;
	}

	public function send_event_email() {
		$events = $this->event_cpt->get_all_available_events();
		$logger = new Logger();
		$logger->log( 'Sending event email' );
		if ( empty( $events ) ) {
			return;
		}

		$user_config = new UserConfig();

		foreach ( $events as $event ) {
			$logger->log( 'Sending event email' );
			if ( $this->check_if_send_email( $event['ID'] ) === false ) {
				continue;
			}
			if ( $this->check_if_email_already_sent( $event['ID'] ) === true ) {
				continue;
			}
			if ( $this->check_if_is_global( $event['ID'] ) ) {
				$recipients = $user_config->get_all_users();
			} else {
				$recipients = $this->get_all_event_recipients_assigned_to_rod( $event );
			}
			if ( empty( $recipients ) ) {
				continue;
			}
			foreach ( $recipients as $user_id ) {
				$this->prepare_email_data( $event, $user_id );
				$this->handle_email_send( $event['ID'] );
			}
		}

	}

	public function get_all_event_recipients_assigned_to_rod( $event ) {
		$get_rods_ids              = get_field( 'rod', $event['ID'] );
		$get_users_assigned_to_rod = array();

		if ( empty( $get_rods_ids ) ) {
			return $get_users_assigned_to_rod;
		}

		$user_config = new UserConfig();
		foreach ( $get_rods_ids as $rod_id ) {
			$single_rod_users = $user_config->get_all_rod_users( $rod_id );
			if ( empty( $single_rod_users ) ) {
				continue;
			}
			$get_users_assigned_to_rod = array_merge( $get_users_assigned_to_rod, $single_rod_users );
			$get_users_assigned_to_rod = array_unique( $get_users_assigned_to_rod );
		}
		return $get_users_assigned_to_rod;

	}


	public function prepare_email_data( $event, $user_id ) {
		$subject = 'Nadchodzące wydarzenie';
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );
		$message = $this->get_event_email_message( $event['ID'] );
		$to      = $this->get_user_email( $user_id );
		$this->config_email( $to, $subject, $message, $headers );
	}

}
