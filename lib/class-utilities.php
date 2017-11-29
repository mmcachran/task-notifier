<?php
/**
 * Utilities Class
 *
 * @package Basecamp RSS Feed Parser
 */

/**
 * Class to hold utilities.
 */
class Utilities {
	/**
	 * Unserialize a variable if serialized
	 *
	 * @param  mixed $var var to possibly unserialize.
	 * @return mixed      unserialized var if input was serialized, otherwise original input.
	 */
	public static function maybe_unserialize( $var ) {
		return self::is_serialized( $var ) ? unserialize( $var ) : $var; // @codingStandardsIgnoreLine
	}

	/**
	 * Check to see if a var is serialize.
	 *
	 * @param  mixed $var Var to test against.
	 * @return boolean    True if string is serialized, false otherwise
	 */
	public static function is_serialized( $var ) {
		// Bail early if not a string.
		if ( ! is_string( $var ) ) {
			return false;
		}

		return ( false === @unserialize( $var ) ) ? false : true; // @codingStandardsIgnoreLine
	}

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

	/**
	 * Get the class to add to the table row based on days since last update.
	 *
	 * @param  int $diff Days since the last update.
	 * @return string    Class to add to table row.
	 */
	public static function get_time_diff_class( $diff ) {
		switch ( true ) {
			case ( (int) $diff > 3 ) :
				return 'red';

			case ( (int) $diff > 1 ) :
				return 'yellow';

			default:
				return '';
		}
	}
}
