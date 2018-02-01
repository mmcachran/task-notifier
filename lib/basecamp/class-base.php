<?php
/**
 * Basecamp class
 *
 * @package  Basecamp RSS Feed Parser
 */

namespace Basecamp;

/**
 * Class to handle Basecamp RSS feed parsing.
 */
class Base {
	/**
	 * Base URL for API requests
	 */
	const API_BASE = 'https://launchpad.37signals.com/';

	/**
	 * Holds OAuth tokens
	 *
	 * @var  array
	 */
	protected $oauth_tokens = array();

	/**
	 * Holds last time the script was run
	 *
	 * @var boolean
	 */
	protected $last_run = false;

	/**
	 * Are we in debug mode?
	 *
	 * @var boolean
	 */
	protected $debug = false;

	/**
	 * BC ID
	 *
	 * @var string
	 */
	protected $bc_id = '';

	/**
	 * BC Client ID
	 *
	 * @var string
	 */
	protected $client_id = '';

	/**
	 * BC Client Secret
	 *
	 * @var string
	 */
	protected $client_secret = '';

	/**
	 * Redirect URI.
	 *
	 * @var string
	 */
	protected $redirect_uri = '';

	/**
	 * Constructor for BC class
	 *
	 * @return void
	 */
	protected function __construct() {
		$this->debug = isset( $_GET['debug'] ) ? true : false; // @codingStandardsIgnoreLine

		// Attempt to fetch OAuth tokens and last run info from the database.
		$this->oauth_tokens = \Option::get( 'bc_tokens' );
		$this->last_run     = ! $this->debug ? \Option::get( 'bc_last_run' ) : false;
	}

	/**
	 * Preforms the BC authentication.
	 *
	 * @return void
	 */
	public function do_auth() {
		// Attempt to refresh tokens.
		$this->attempt_to_refresh_tokens();

		// Check for expired token and attempt to refresh again if necessary.
		if ( ! empty( $this->oauth_tokens ) ) {
			$this->attempt_to_refresh_tokens();
		}

		// Authorize user.
		if ( empty( $this->oauth_tokens ) && ! isset( $_GET['code'] ) ) { // @codingStandardsIgnoreLine
			$this->authorize();
		}

		// Get the oauth token.
		if ( isset( $_GET['code'] ) && empty( $this->oauth_tokens ) ) { // @codingStandardsIgnoreLine
			$this->oauth_tokens = $this->get_tokens( $_GET['code'] ); // @codingStandardsIgnoreLine
		}
	}

	/**
	 * OAuth authorization endpoint to obtain code for getting tokens
	 *
	 * @return void
	 */
	protected function authorize() {
		$params = array(
			'type' 		   => 'web_server',
			'client_id'    => $this->client_id,
			'redirect_uri' => $this->redirect_uri,
		);

		$authorization_url = self::API_BASE . 'authorization/new?' . http_build_query( $params );

		// Redirect the user for authorization.
		\Utilities\Redirect::redirect( $authorization_url );
	}

	/**
	 * Get OAuth tokens
	 *
	 * @param  string $code 	 Code received to get OAuth tokens.
	 * @return object|bool       Token object on success, false on failure.
	 */
	protected function get_tokens( $code ) {
		$params = array(
			'type'          => 'web_server',
			'client_id'     => $this->client_id,
			'client_secret' => $this->client_secret,
			'redirect_uri'  => $this->redirect_uri,
			'code'          => $code,
		);

		// Build the authorization token request.
		$token_url = self::API_BASE . 'authorization/token';

		$response = \Request::post( $token_url, $params );

		// Bail early if no response.
		if ( empty( $response ) ) {
			return false;
		}

		// Save timestamp.
		$response->timestamp = strtotime( 'now' );

		// Update tokens option.
		\Option::update( 'bc_tokens', $response );

		return $response;
	}

	/**
	 * Determine if token refresh is needed
	 *
	 * @return void
	 */
	protected function attempt_to_refresh_tokens() {
		// Bail early if no timestamp.
		if ( empty( $this->oauth_tokens->timestamp ) ) {
			return;
		}

		// Bail early if no expires_in.
		if ( empty( $this->oauth_tokens->expires_in ) ) {
			return;
		}

		// Determine if the token timestamp plus expiration time is in the past.
		$expires_time = (int) $this->oauth_tokens->timestamp + (int) $this->oauth_tokens->expires_in;

		// Refresh if needed.
		if ( $expires_time < strtotime( 'now' ) ) {
			$this->get_token_from_refresh_token();
		}
	}

	/**
	 * Refresh OAuth tokens
	 *
	 * @return string The refreshed token
	 */
	protected function get_token_from_refresh_token() {
		$params = array(
			'type'          => 'refresh',
			'client_id'     => $this->client_id,
			'redirect_uri'  => $this->redirect_uri,
			'client_secret' => $this->client_secret,
			'refresh_token' => $this->oauth_tokens->refresh_token,
		);

		$token_url = self::API_BASE . 'authorization/token';

		$response = \Request::post( $token_url, $params );

		// Bail early if no response.
		if ( empty( $response ) ) {
			return false;
		}

		// Add refresh token and timestamp.
		$response->refresh_token = $this->oauth_tokens->refresh_token;
		$response->timestamp     = strtotime( 'now' );

		// Update tokens option.
		\Option::update( 'bc_tokens', $response );

		return $response;
	}

	/**
	 * Get a list of projects
	 *
	 * @return array|bool project array or false on failure
	 */
	protected function get_projects() {
		// Force authorization if no tokens.
		if ( empty( $this->oauth_tokens ) ) {
			$this->authorize();
		}

		$projects_url = 'https://basecamp.com/' . $this->bc_id . '/api/v1/projects.json';

		$auth_args = array(
			'token' => $this->oauth_tokens->access_token,
		);

		return \Request::get( $projects_url, $auth_args );
	}

	/**
	 * Get a list of topics from a given page number (50 per page sorted by newest first)
	 *
	 * @param  integer $page Page to fetch.
	 * @return array|bool    Topics or false on failure
	 */
	protected function get_topics( $page = 1 ) {
		// Bail early if no oauth access tokens.
		if ( empty(  $this->oauth_tokens->access_token ) ) {
			return false;
		}

		$topics_url = 'https://basecamp.com/' . $this->bc_id . '/api/v1/topics.json';

		// Add additional params to request url.
		$params = array(
			'page' => $page,
			'sort' => 'newest',
		);

		$topics_url .= '?' . http_build_query( $params );

		// Add Bearer token to request.
		$auth_args = array(
			'token' => $this->oauth_tokens->access_token,
		);

		$results = \Request::get( $topics_url, $auth_args );

		// Bail early if no results.
		if ( empty( $results ) ) {
			return false;
		}

		return $results;
	}

	/**
	 * Sets the BC ID.
	 *
	 * @param string $bc_id The BC ID.
	 * @return void
	 */
	public function set_bc_id( $bc_id ) {
		$this->bc_id = $bc_id;
	}

	/**
	 * Sets the BC client ID.
	 *
	 * @param string $client_id The BC client ID.
	 * @return void
	 */
	public function set_client_id( $client_id ) {
		$this->client_id = $client_id;
	}

	/**
	 * Sets the BC client secret.
	 *
	 * @param string $client_secret The BC client secret.
	 * @return void
	 */
	public function set_client_secret( $client_secret ) {
		$this->client_secret = $client_secret;
	}

	/**
	 * Sets the BC redirect uri.
	 *
	 * @param string $redirect_uri The BC client ID.
	 * @return void
	 */
	public function set_redirect_uri( $redirect_uri ) {
		$this->redirect_uri = $redirect_uri;
	}
}
