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
	 * @var \Slack
	 */
	protected static $instance = null;

	/**
	 * Priority lists. todolist_id => Slack room.
	 *
	 * @var array
	 */
	protected $priority_lists = array();

	/**
	 * Slack channel mapping
	 * Text to match on bucket name => Slack room.
	 *
	 * @var array
	 */
	protected $channel_mapping = array();

	/**
	 * Returns a single instance of the class
	 *
	 * @return Slack
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
		$msg = strip_tags( $msg ); // @codingStandardsIgnoreLine

		// Encode the message.
		$msg = str_replace( '&', '&amp;', $msg );
		$msg = str_replace( '<', '&lt;', $msg );
		$msg = str_replace( '>', '&gt;', $msg );

		$fields = array(
			'channel'    => $channel,
			'text'       => $msg,
			'icon_emoji' => ':ghost:',
		);

		// Encode the conversation fields.
		$data = 'payload=' . json_encode( $fields ); // @codingStandardsIgnoreLine

		// Post the message.
		$response = Request::post( self::WEBHOOK_URL, $data );
	}

	/**
	 * Helper method to determine where updates should go in Slack.
	 *
	 * @param  object $topic Topic object.
	 * @return string        Slack room or false if the mapping doesn't exist.
	 */
	public static function get_project_channel( $topic ) {
		// Bail early if the list matches.
		if ( ! empty( $this->priority_lists[ $topic->topic_info->todolist_id ] ) ) {
			return $this->priority_lists[ $topic->topic_info->todolist_id ];
		}

		// Loop through and find the channel.
		foreach ( $this->slack_channel_mapping as $name => $channel ) {
			// Skip if not the correct channel.
			if ( ! stristr( $topic->bucket->name, $name ) ) {
				continue;
			}

			// Return the channel.
			return $channel;
		}

		// Return the default channel.
		return false;
	}

	/**
	 * Sets priority lists for Slack channel mapping.
	 *
	 * @param array $priority_lists Any todo lists that should always be sent to Slack.
	 * @return void
	 */
	public function set_priority_lists( array $priority_lists ) {
		$this->priority_lists = $priority_lists;
	}

	/**
	 * Sets priority lists for Slack channel mapping.
	 *
	 * @param array $channel_mapping Slack channel mapping for BC projects.
	 * @return void
	 */
	public function set_channel_mapping( array $channel_mapping ) {
		$this->channel_mapping = $channel_mapping;
	}
}
