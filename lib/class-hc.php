<?php
/**
 * HipChat
 *
 * @package  Basecamp RSS Feed Parser
 */

/**
 * Class for HipChat functionality
 */
class HC {
	/**
	 * Auth token for HC
	 */
	const HC_TOKEN = '';

	/**
	 * HC room for notifications to go
	 */
	const HC_ROOM = '';

	/**
	 * Holds instance of HC lib class
	 *
	 * @var HipChat
	 */
	protected $hc = false;

	/**
	 * Holds single instance of this class
	 *
	 * @var HC
	 */
	protected static $instance = null;

	/**
	 * Returns a single instance of the class
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class constructor
	 */
	protected function __construct() {
		require_once BASE_DIR . '/vendor/HipChat.php';
		$this->hc = new HipChat\HipChat( self::HC_TOKEN );
	}

	/**
	 * Send the message to HC room
	 *
	 * @param  string $msg       Message body.
	 * @param  string $from      Label to be shown in addition to senders name.
	 * @param  string $msg_color Color for message (yellow, green, red, purple, gray, random).
	 */
	public function send_message( $msg, $from = 'BC SLA', $msg_color = 'red' ) {
		$this->hc->message_room( self::HC_ROOM, $from, $msg, true, $msg_color );
	}
}
