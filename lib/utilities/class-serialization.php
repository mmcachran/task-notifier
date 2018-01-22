<?php
/**
 * Serialization Class
 *
 * @package Basecamp RSS Feed Parser
 */

namespace Utilities;

/**
 * Class to hold serialization utilities.
 */
class Serialization {
	/**
	 * Unserialize a variable if serialized
	 *
	 * @param  mixed $var Variable to possibly unserialize.
	 * @return mixed      Unserialized var if input was serialized, otherwise original input.
	 */
	public static function maybe_unserialize( $var ) {
		return self::is_serialized( $var ) ? unserialize( $var ) : $var; // @codingStandardsIgnoreLine
	}

	/**
	 * Check to see if a var is serialize.
	 *
	 * @param  mixed $var Variable to test against.
	 * @return boolean    True if string is serialized, false otherwise
	 */
	public static function is_serialized( $var ) {
		// Bail early if not a string.
		if ( ! is_string( $var ) ) {
			return false;
		}

		return ( false === @unserialize( $var ) ) ? false : true; // @codingStandardsIgnoreLine
	}
}
