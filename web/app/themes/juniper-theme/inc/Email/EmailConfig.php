<?php
namespace Dzialkowik\Email;

use Dzialkowik\Logger\Logger;

class EmailConfig {

	private $to;
	private $subject;
	private $message;
	private $headers;
	private $attachments;

	public function config_email( string $to, string $subject, string $message, $headers = '', string $attachments = '' ) {
		$this->to          = $to;
		$this->subject     = $subject;
		$this->message     = $message;
		$this->headers     = $headers;
		$this->attachments = $attachments;
	}

	public function validate_strings( string $subject ) {
		return filter_var( $subject, FILTER_SANITIZE_STRING );
	}

	public function validate_email( string $email ) {
		return filter_var( $email, FILTER_VALIDATE_EMAIL );
	}

	public function get_to(): string {
		return $this->validate_email( $this->to );
	}

	public function get_subject(): string {
		return $this->validate_strings( $this->subject );
	}

	public function get_message(): string {
		return $this->message;
	}

	public function get_headers(): mixed {
		if ( empty( $this->headers ) ) {
			return '';
		}
		if ( is_array( $this->headers ) ) {
			$headers = '';
			foreach ( $this->headers as $header ) {
				$headers .= $header . "\r\n";
			}
			return $headers;
		}
		return $this->headers;
	}

	public function get_attachments(): string {
		return $this->attachments;
	}

	public function send_email() {
		return wp_mail(
			$this->get_to(),
			$this->get_subject(),
			$this->get_message(),
			$this->get_headers(),
			$this->get_attachments()
		);
	}

	public function get_user_email( $user_id ) {
		$user = get_user_by( 'id', $user_id );
		if ( $user && ! empty( $user->user_email ) ) {
			return $user->user_email;
		}
		return false;
	}

	public function handle_email_send() {
		if ( ! $this->send_email() ) {
			$logger = new Logger();
			$logger->log( 'Error sending email' );
		}
	}
}
