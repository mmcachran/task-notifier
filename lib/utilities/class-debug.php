<?php
/**
 * Debugging class
 *
 * @package Basecamp RSS Feed Parser
 */

namespace Utilities;

/**
 * Class for debugging functionality.
 */
class Debug {
	/**
	 * Directory for the debug logs.
	 *
	 * @var  string
	 */
	const DEBUG_LOG_DIR = '/logs/';

	/**
	 * Displays a debug message at the current position on the page.
	 *
	 * @return void
	 */
	public static function dump() {
		// Keep track of if styles have been added or not.
		static $need_styles = true;

		// Add styles if needed.
		if ( $need_styles ) :
			?>
			<style>
			.debug {
				break: both;
				border: 1px solid #C00;
				background: rgba(255, 200, 200, 0.8);
				padding: 10px;
				margin: 10px;
				position: relative;
				z-index: 99999;
				box-shadow: 0 1px 5px rgba(0,0,0,0.3);
				font-size: 12px;
			}
			.debug:before {
				content: 'DEBUG';
				font-size: 11px;
				position: absolute;
				right: 0;
				top: 0;
				color: #FFF;
				background-color: #D88;
				padding: 2px 8px;
			}
			.debug .debug-wrap {
				box-shadow: 0 1px 5px rgba(0,0,0,0.18);
			}
			.debug pre {
				font-size: 12px !important;
				margin: 1px 0 !important;
				background: rgba(255, 200, 200, 0.8);
			}
			</style>
			<?php
			$need_styles = false;
		endif; // End if().

		?>
		<div class="debug"><div class="debug-wrap">
			<?php

			// Loop through all function arguments.
			foreach ( func_get_args() as $param ) {
				echo '<pre>';
				var_dump( $param ); // @codingStandardsIgnoreLine
				echo '</pre>';
			}

			?>
			<table class="trace" cellspacing="0" cellpadding="2" border="1">
				<?php
				// Loop through backtrace.
				foreach ( debug_backtrace() as $id => $item ) { // @codingStandardsIgnoreLine
					printf(
						'<tr><td>%1$s</td><td>%2$s : %3$s</td></tr>',
						$id, // @codingStandardsIgnoreLine
						@$item['file'], // @codingStandardsIgnoreLine
						@$item['line'] // @codingStandardsIgnoreLine
					);
				}
				?>
			</table>
		</div></div>
		<?php
	}

	/**
	 * Log messages to file
	 *
	 * @param  string $message Message to log.
	 * @return void
	 */
	public static function log( $message ) {
		// Determine where the debug log is.
		$log_dir = BASE_DIR . self::DEBUG_LOG_DIR;

		// Bail early if directory doesn't exist.
		if ( ! is_writable( $log_dir ) ) { // @codingStandardsIgnoreLine
			return;
		}

		// Check if message is not a string.
		if ( ! is_string( $message ) ) {
			$message = print_r( $message, true ); // @codingStandardsIgnoreLine
		}

		// Suppress errors in case file doesn't exist.
		@file_put_contents( $log_dir . 'debug.log', "\n[" . date( 'd-M-Y h:i:s A' ) . '] ' . $message, FILE_APPEND ); // @codingStandardsIgnoreLine
	}
}
