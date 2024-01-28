<?php

namespace Dzialkowik\Logger;

class Logger {

	protected string $logger_file;

	public function __construct() {
		$this->logger_file = get_template_directory() . LOGGER_FILE;
	}

	public function log( $message ) {
		$error_log = '[' . gmdate( 'Y-m-d H:i:s', time() ) . '] - ' . $message . PHP_EOL;
		file_put_contents( $this->logger_file, $error_log, FILE_APPEND );
	}
}
