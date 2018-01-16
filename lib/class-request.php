<?php
/**
 * Class to handle API requests.
 *
 * @package Basecamp RSS Feed Parser
 */

/**
 * Request class.
 */
class Request {
	/**
	 * Use curl to get a requested url.
	 *
	 * @param  string  $url        URL to fetch.
	 * @param  array   $args       Arguments for http auth.
	 * @param  boolean $return_all Return info and output or just output.
	 * @return array    		   Data returned from request.
	 */
	public static function get( $url, $args, $return_all = false ) {
		$ch = curl_init(); // @codingStandardsIgnoreLine
		curl_setopt( $ch, CURLOPT_URL, $url ); // @codingStandardsIgnoreLine
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true ); // @codingStandardsIgnoreLine

		// Set username and password.
		if ( isset( $args['token'] ) ) {
			curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Authorization: Bearer ' . $args['token'] ) ); // @codingStandardsIgnoreLine
		}

		// Set userpwd if provided.
		if ( isset( $args['userpwd'] ) ) {
			curl_setopt( $ch, CURLOPT_USERPWD, $args['userpwd'] ); // @codingStandardsIgnoreLine
		}

		// Set user agent.
		curl_setopt( $ch, CURLOPT_USERAGENT, 'WebDevStudios (http://webdevstudios.com)' ); // @codingStandardsIgnoreLine

		$response = curl_exec( $ch ); // @codingStandardsIgnoreLine
		$info = curl_getinfo( $ch ); // @codingStandardsIgnoreLine
		curl_close( $ch ); // @codingStandardsIgnoreLine

		// Bail early if bad response code.
		if ( ! ( 200 === $info['http_code'] ) ) {
			return false;
		}

		// Bail early if no response.
		if ( empty( $response ) ) {
			return false;
		}

		// Decode output.
		$response = json_decode( $response );

		// If info and output are required.
		if ( $return_all ) {
			return array(
				'response' => $response,
				'info'     => $info,
			);
		}

		return $response;
	}

	/**
	 * Fetch response from Curl POST.
	 *
	 * @param  string $url    URL for request.
	 * @param  array  $fields Fields to post.
	 * @param  array  $args   Arguments for the request.
	 * @return array          Response from API
	 */
	public static function post( $url, $fields = array(), $args = array() ) {
		// Open connection.
		$ch = curl_init(); // @codingStandardsIgnoreLine

		// Set the url, number of POST vars, POST data.
		curl_setopt( $ch, CURLOPT_URL, $url ); // @codingStandardsIgnoreLine

		if ( is_array( $fields ) ) {
			curl_setopt( $ch, CURLOPT_POST, count( $fields ) ); // @codingStandardsIgnoreLine
			curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $fields ) ); // @codingStandardsIgnoreLine
		} else {
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $fields ); // @codingStandardsIgnoreLine

			if ( isset( $args['httpheader'] ) ) {
				curl_setopt( $ch, CURLOPT_HTTPHEADER, $args['httpheader'] ); // @codingStandardsIgnoreLine
			}
		}

		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true ); // @codingStandardsIgnoreLine

		// Set userpwd if provided.
		if ( isset( $args['userpwd'] ) ) {
			curl_setopt( $ch, CURLOPT_USERPWD, $args['userpwd'] ); // @codingStandardsIgnoreLine
		}

		// Execute post.
		$response = curl_exec( $ch ); // @codingStandardsIgnoreLine

		// Close connection.
		curl_close( $ch ); // @codingStandardsIgnoreLine

		// bail early if no response.
		if ( empty( $response ) ) {
			return false;
		}

		return json_decode( $response );
	}
}
