<?php
/**
 * Redirect Class
 *
 * @package Basecamp RSS Feed Parser
 */

namespace Utilities;

/**
 * Class to hold redirect utilities.
 */
class Redirect {

	/**
	 * Redirect the user
	 *
	 * @param  string $url URL to redirect user to.
	 * @return void
	 */
	public static function redirect( $url ) {
		header( 'Location: ' . $url );
		exit( 0 );
	}
}
