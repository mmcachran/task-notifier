<?php
/**
 * Handles HelpScout functionality.
 *
 * @package  Basecamp RSS Feed Parser
 */

/**
 * HelpScout
 */
class HelpScout {
	/**
	 * Endpoint to create a conversation
	 */
	const CREATE_CONVERSATION_ENDPOINT = 'https://api.helpscout.net/v1/conversations.json';

	/**
	 * Endpoint to update a conversation
	 */
	const UPDATE_CONVERSATION_ENDPOINT = 'https://api.helpscout.net/v1/conversations/{id}.json';

	/**
	 * Get conversations endpoint
	 */
	const GET_CONVERSATIONS_ENDPOINT = 'https://api.helpscout.net/v1/mailboxes/{mailbox_id}/conversations.json';

	/**
	 * Holds single instance of this class
	 *
	 * @var HelpScout
	 */
	protected static $instance = null;

	/**
	 * Conversation tags to use when querying or creating a conversation
	 *
	 * @var array
	 */
	protected static $conversation_tags = array(
		'basecamp'
	);

	/**
	 * Returns a single instance of the class
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Class constructor
	 *
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Create a HS conversation
	 *
	 * @param  array $conversation create a helpscout conversation.
	 * @return bool               true on success, false on failure
	 */
	public function create_conversation( $conversation = array() ) {
		// Bail early if no conversation.
		if ( empty( $conversation ) ) {
			return;
		}

		// Set default fields.
		$fields = array(
			'mailbox' => array(
				'id' => HS_MAILBOX_ID,
			),
			'customer' => array(
				'email' => HS_CUSTOMER_EMAIL,
			),
			'tags' => self::$conversation_tags,
		);

		// Merge fields together and encode for post request.
		$fields = array_merge( $fields, $conversation );
		$fields = json_encode( $fields ); // @codingStandardsIgnoreLine

		// Fetch default args.
		$args = self::get_default_args();

		// Set the headers.
		$args['httpheader'] = array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen( $fields ),
		);

		$response = Request::post( self::CREATE_CONVERSATION_ENDPOINT, $fields, $args );
	}

	/**
	 * Append a conversation to a HS thread
	 *
	 * @param  int   $id           ID of thread to append to.
	 * @param  array $conversation Conversation to append.
	 * @return bool                True on success, false on failure.
	 */
	public function append_thread_to_conversation( $id, $conversation ) {
		$update_conversation_endpoint = str_replace( '{id}', $id, self::UPDATE_CONVERSATION_ENDPOINT );

		$fields = array(
			'mailbox' => HS_MAILBOX_ID,
			'customer' => array(
				'email' => HS_CUSTOMER_EMAIL,
			),
			'body' => $conversation['threads'][0]['body'],
			'createdBy' => $conversation['threads'][0]['createdBy'],
			'type' => 'message',
		);

		// encode the conversation fields.
		$fields = json_encode( $fields );

		// fetch default args.
		$args = self::get_default_args();

		// set the headers.
		$args['httpheader'] = array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen( $fields ),
		);

		return Request::post( $update_conversation_endpoint, $fields, $args );
	}

	/**
	 * Returns a list of all HS conversations tagged with "basecamp"
	 *
	 * @return array Response.
	 */
	public function get_bc_hs_conversations() {
		// holds conversations indexed by BC ID.
		$response = array();

		// get all pages of conversations.
		$items = self::get_all_hs_conversations();

		// bail early if no conversations.
		if ( empty( $items ) ) {
			return $response;
		}

		// loop through and create array for comparison.
		foreach ( (array) $items as $conversation ) {
			$parts = explode( '|', $conversation->subject );

			// skip if not more than one part.
			if ( ! is_array( $parts ) || 2 > count( $parts ) ) {
				continue;
			}

			$bc_id = $parts[ count( $parts ) - 1 ];
			$bc_id = trim( $bc_id );

			// skip if no id.
			if ( empty( $bc_id ) || ! is_numeric( $bc_id ) ) {
				continue;
			}

			// add to response array.
			$response[ $bc_id ] = $conversation->id;
		}

		return $response;
	}

	/**
	 * Get all pages of HS conversations
	 *
	 * @return array array of all conversations
	 */
	protected static function get_all_hs_conversations() {
		// fire initial request.
		$conversations = self::get_bc_hs_conversations_by_page( 1 );

		// bail early if no items.
		if ( empty( $conversations->items ) ) {
			return $response;
		}

		// hold all items from every page.
		$items = $conversations->items;

		// bail early if no more pages.
		if ( 1 === $conversations->pages ) {
			return $items;
		}

		for ( $i = 2; $i <= $conversation->pages; ++$i ) {
			// fire initial request.
			$conversations = self::get_bc_hs_conversations_by_page( $i );

			// bail early if no items.
			if ( empty( $conversations->items ) ) {
				continue;
			}

			$items = array_merge( $items, $conversations->items );
		}

		return $items;
	}

	/**
	 * Get conversations from a specific page number
	 *
	 * @param  integer $page page to fetch.
	 * @return object        API response
	 */
	protected static function get_bc_hs_conversations_by_page( $page = 1 ) {
		$request = str_replace( '{mailbox_id}', HS_MAILBOX_ID, self::GET_CONVERSATIONS_ENDPOINT );

		$args = array(
			'page' => $page,
			'tag' => implode( ',', self::$conversation_tags ),
			//'status' => 'active',
		);

		$request .= '?' . http_build_query( $args );

		return Request::get( $request, self::get_default_args() );
	}

	/**
	 * Get default args for API auth
	 *
	 * @return array array of default args.
	 */
	protected static function get_default_args() {
		return array(
			'userpwd' => HS_API_KEY . ':X',
		);
	}
}