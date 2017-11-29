<?php
/**
 * Basecamp new tasks class
 *
 * @package  Basecamp RSS Feed Parser
 */

/**
 * Class to handle Basecamp RSS feed parsing for new tasks.
 */
class Basecamp_New_Tasks extends Basecamp {
	/**
	 * Holds single instance of this class
	 *
	 * @var Basecamp_New_Tasks
	 */
	protected static $instance = null;

	/**
	 * Buckets to allowed to send notifications to a slack channel.
	 *
	 * @var array
	 */
	protected $allowed_projects = array(
		'support',
		'bulk hours',
		'retainer',
		'webdevstudios',
	);

	/**
	 * Topics to allowed to send notifications to a slack channel.
	 *
	 * @var array
	 */
	protected $allowed_topics = array(
		'critical',
		'urgent',
		'p0',
		'p1',
	);

	/**
	 * Projects to exclude from adding notifications to a Slack channel.
	 *
	 * @var array
	 */
	protected $excluded_projects = array(
		'Viacom Blog',
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
	 * Get new topics since last run date.
	 *
	 * @return array|bool new topics on success, false on failure.
	 */
	public function get_new_topics() {
		// Fetch topics.
		$topics = $this->get_topics();

		// Bail early if no topics.
		if ( empty( $topics ) ) {
			return false;
		}

		// Loop through and only return results after the timestamp.
		foreach ( (array) $topics as $key => $topic ) {
			// Remove if topic is before last run.
			if ( ! empty( $this->last_run ) && ( strtotime( $topic->updated_at ) < $this->last_run ) ) {
				unset( $topics[ $key ] );
				continue;
			}

			// Remove topic if not valid support topic.
			if ( ! $this->is_valid_support_topic( $topic ) ) {
				unset( $topics[ $key ] );
				continue;
			}

			// Add Bearer token to request.
			$auth_args = array(
				'token' => $this->oauth_tokens->access_token,
			);

			// Add additional info to the topic.
			$topics[ $key ]->topic_info = Request::get( $topic->topicable->url, $auth_args );
		}

		// Update last run.
		Option::update( 'bc_last_run', strtotime( 'now' ) );

		return $topics;
	}

	/**
	 * Determin if topic is a valid support topic
	 *
	 * @param  object $topic BC topic object to test.
	 * @return boolean        true if valid, false if not
	 */
	protected function is_valid_support_topic( $topic ) {
		// Bail early if this project isn't allowed.
		foreach ( (array) $this->excluded_projects as $exlude ) {
			// Skip if project doesn't match.
			if ( ! stristr( $topic->bucket->name, $exlude ) ) {
				continue;
			}

			// This project is exluded.
			return false;
		}

		// Bail early if support is in the project title.
		if ( stristr( $topic->bucket->name, 'support' ) ) {
			return true;
		}

		// Bail early if this project is allowed.
		foreach ( (array) $this->allowed_projects as $allowed ) {
			// Skip if project doesn't match.
			if ( ! stristr( $topic->bucket->name, $allowed ) ) {
				continue;
			}

			// This project is allowed.
			return true;
		}

		// Bail early if this topic is allowed.
		foreach ( (array) $this->allowed_topics as $allowed ) {
			// Skip if topic doesn't match.
			if ( ! stristr( $topic->title, $allowed ) ) {
				continue;
			}

			// This project is allowed.
			return true;
		}

		// This task shouldn't notify a Slack channel.
		return false;
	}
}
