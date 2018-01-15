<?php
/**
 * Handles caching.
 *
 * @package Basecamp RSS Feed Parser
 */

/**
 * Cache.
 */
class Cache {
	/**
	 * Fetch tokens from cache
	 *
	 * @return object|bool Token object if true, false on failure.
	 */
	public static function fetch_tokens() {
		$cache_file = BASE_DIR . '/cache/tokens.txt';

		// Bail early if no cache.
		if ( ! file_exists( $cache_file ) ) {
			return false;
		}

		$cache = file_get_contents( $cache_file ); // @codingStandardsIgnoreLine

		return json_decode( $cache );
	}

	/**
	 * Cache tokens after successful request
	 *
	 * @param  object $tokens tokens object from API.
	 * @return void
	 */
	public static function save_tokens( $tokens ) {
		$cache_file = BASE_DIR . '/cache/tokens.txt';

		// Bail early if no cache.
		if ( ! file_exists( $cache_file ) ) {
			return false;
		}

		file_put_contents( $cache_file, json_encode( $tokens ) ); // @codingStandardsIgnoreLine
	}

	/**
	 * Get the last run timestamp.
	 *
	 * @return string Last run timestamp.
	 */
	public static function get_last_run_timestamp() {
		$cache_file = BASE_DIR . '/cache/last_run.txt';

		// Bail early if no cache.
		if ( ! file_exists( $cache_file ) ) {
			return false;
		}

		$cache = file_get_contents( $cache_file ); // @codingStandardsIgnoreLine

		return $cache;
	}

	/**
	 * Cache timestamp after successful request.
	 *
	 * @return void
	 */
	public static function save_last_run() {
		$cache_file = BASE_DIR . '/cache/last_run.txt';

		// Bail early if no cache.
		if ( ! file_exists( $cache_file ) ) {
			return false;
		}

		file_put_contents( $cache_file, strtotime( 'now' ) ); // @codingStandardsIgnoreLine
	}
}
