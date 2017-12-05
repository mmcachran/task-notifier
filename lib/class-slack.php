<?php
/**
 * Slack integration
 *
 * @package Basecamp RSS Feed Parser
 */

/**
 * Class to send messages to Slack.
 */
class Slack {
	/**
	 * Webhook for Slack
	 */
	const WEBHOOK_URL = '';

	/**
	 * Holds single instance of this class
	 *
	 * @var Slack
	 */
	protected static $instance = null;

	/**
	 * Returns a single instance of the class
	 *
	 * @return Slack
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Class constructor
	 */
	protected function __construct() {}

	/**
	 * Send the message to a Slack channel
	 *
	 * @param  string $msg       Message body.
	 * @param  string $channel 	 The channel to post the message in.
	 * @return void
	 */
	public function send_message( $msg, $channel = '' ) {
		// Bail early if no channel.
		if ( empty( $channel ) ) {
			return;
		}

		// Strip tags in the message.
		$msg = strip_tags( $msg );

		// Encode the message.
		$msg = str_replace( '&', '&amp;', $msg );
		$msg = str_replace( '<', '&lt;', $msg );
		$msg = str_replace( '>', '&gt;', $msg );

		$fields = array(
			'channel' => $channel,
			'text' => $msg,
			'icon_emoji' => ':ghost:',
		);

		// Encode the conversation fields.
		$data = 'payload=' . json_encode( $fields );

		// Post the message.
		$response = Request::post( self::WEBHOOK_URL, $data );
	}
}
