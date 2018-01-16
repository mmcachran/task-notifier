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
