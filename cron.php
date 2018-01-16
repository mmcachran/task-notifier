<?php
/**
 * Runs on a cron to put BC messages into Slack.
 *
 * @package Basecamp RSS Feed Parser
 */

// Sets the default timezone.
date_default_timezone_set( 'America/New_York' ); // @codingStandardsIgnoreLine

// Set a constant for the base directory path.
define( 'BASE_DIR', realpath( dirname( __FILE__ ) ) );

// Include config file.
if ( file_exists( BASE_DIR . '/config.php' ) ) {
	require_once BASE_DIR . '/config.php' ;
}

// Include autoloader file.
if ( file_exists( BASE_DIR . '/lib/class-autoloader.php' ) ) {
	require_once BASE_DIR . '/lib/class-autoloader.php';
}

// Check for un and pw to be defined.
if ( ! defined( 'BC_CLIENT_ID' ) || ! defined( 'BC_CLIENT_SECRET' ) ) {
	echo 'BC_CLIENT_ID or BC_CLIENT_SECRET not defined. Define these in a config file in the document root.';
	exit( 0 );
}

if ( ! function_exists( 'get_project_slack_channel' ) ) :
	/**
	 * Function to determine where updates should go in Slack.
	 *
	 * @param  object $topic Topic object.
	 * @return string        Slack room or false if the mapping doesn't exist.
	 */
	function get_project_slack_channel( $topic ) {
		// Priority lists. id => Slack room.
		$priority_lists = array();

		// Bail early if the list matches.
		if ( isset( $priority_lists[ $topic->topic_info->todolist_id ] ) ) {
			return $priority_lists[ $topic->topic_info->todolist_id ];
		}

		/**
		 * Slack channel mapping
		 * Text to match on bucket name => Slack room.
		 *
		 * @var array
		 */
		$slack_channel_mapping = array();

		// Loop through and find the channel.
		foreach ( $slack_channel_mapping as $name => $channel ) {
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
endif;

// Get an instance of BC class and fetch new topics.
$basecamp = \Basecamp\Basecamp_New_Tasks::get_instance();
$topics   = $basecamp->get_new_topics();

// Bail early if no topics.
if ( empty( $topics ) ) {
	return;
}

// Instantiate the Slack class.
$slack = \Slack::get_instance();

// Loop through and create conversations.
foreach ( (array) $topics as $topic ) {
	$message  = $topic->bucket->name . "\n";
	$message .= 'Title: ' . $topic->title . "\n";
	$message .= 'From: ' . $topic->last_updater->name . "\n";

	// Get topic body.
	if ( ! empty( $topic->topic_info->comments ) ) {
		$message .= $topic->topic_info->comments[ count( $topic->topic_info->comments ) - 1 ]->content;
	} else {
		$message .= $topic->excerpt;
	}

	$message .= "\n";

	$message .= $topic->topicable->app_url;

	// Get the project channel.
	$project_channel = get_project_slack_channel( $topic );

	// Send the message to the project channel if available.
	if ( ! ( false === $project_channel ) ) {
		$slack->send_message( $message, $project_channel );
	}
}
