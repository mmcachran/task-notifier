<?php
/**
 * Runs on a cron to put BC messages into Slack.
 *
 * @package Basecamp RSS Feed Parser
 */

// Set a constant for the base directory path.
define( 'BASE_DIR', realpath( dirname( __FILE__ ) ) );

// Include config file.
if ( file_exists( BASE_DIR . '/loader.php' ) ) {
	require_once BASE_DIR . '/loader.php';
}

// Get an instance of BC new tasks class.
$basecamp = \Basecamp\Basecamp_New_Tasks::get_instance();

// Set some properties for OAuth.
$basecamp->set_bc_id( BC_ID );
$basecamp->set_client_id( BC_CLIENT_ID );
$basecamp->set_client_secret( BC_CLIENT_SECRET );
$basecamp->set_redirect_uri( BC_REDIRECT_URI );

// Fetch new topics.
$topics = $basecamp->get_new_topics();

// Bail early if no topics.
if ( empty( $topics ) ) {
	return;
}

// Instantiate the Slack class.
$slack = \Slack::get_instance();

// Set priority todo lists.
$slack->set_priority_lists( array() );

// Set Slack project channel mapping.
$slack->set_channel_mapping( array() );

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
	$project_channel = $slack::get_project_channel( $topic );

	// Send the message to the project channel if available.
	if ( ! ( false === $project_channel ) ) {
		$slack->send_message( $message, $project_channel );
	}
}
