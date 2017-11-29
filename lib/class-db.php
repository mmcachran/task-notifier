<?php
/**
 * Class to handle DB operations.
 *
 * @package Basecamp RSS Feed Parser
 */

/**
 * Database
 */
class DB {
	/**
	 * Holds db connection
	 *
	 * @var null
	 */
	public static $db = null;

	/**
	 * Returns a DB connection if one doesn't already exist
	 *
	 * @return PDO database connection
	 */
	public static function get_connection() {
		// Bail early if connection already set.
		if ( ! ( null === self::$db ) ) {
			return self::$db;
		}

		// check connection creds.
		self::check_db_credentials();

		// attempt to create connection.
		try {
		  self::$db = new PDO( 'mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS ); // @codingStandardsIgnoreLine
		  self::$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION ); // @codingStandardsIgnoreLine
		} catch ( PDOException $e ) {
		    echo $e->getMessage(); // @codingStandardsIgnoreLine
		}

		// return for use outside this class.
		return self::$db;
	}

	/**
	 * Endpoint for all static method calls not defined in this class as public
	 *
	 * @param  string $name PDO method to call.
	 * @param  array  $args arguments to pass to the PDO method.
	 * @return mixed       results of PDO method call
	 */
	public static function __callStatic( $name, $args ) {
		$callback = array( self::get_connection(), $name );
		return call_user_func_array( $callback, $args );
	}

	/**
	 * Verify DB credentials
	 *
	 * @return  void
	 */
	protected static function check_db_credentials() {
		if ( ! defined( 'DB_HOST' ) ) {
			Debug::dump( 'DB_HOST not defined' );
			exit( 0 );
		}

		if ( ! defined( 'DB_NAME' ) ) {
			Debug::dump( 'DB_NAME not defined' );
			exit( 0 );
		}

		if ( ! defined( 'DB_USER' ) ) {
			Debug::dump( 'DB_USER not defined' );
			exit( 0 );
		}

		if ( ! defined( 'DB_PASS' ) ) {
			Debug::dump( 'DB_PASS not defined' );
			exit( 0 );
		}
	}
}
