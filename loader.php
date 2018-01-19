<?php
/**
 * This file loads in common things between templates.
 *
 * @package Basecamp RSS Feed Parser
 */

// Sets the default timezone.
date_default_timezone_set( 'America/New_York' ); // @codingStandardsIgnoreLine

// Include config file.
if ( file_exists( BASE_DIR . '/config.php' ) ) {
	require_once BASE_DIR . '/config.php' ;
}

// Include autoloader file.
if ( file_exists( BASE_DIR . '/lib/class-autoloader.php' ) ) {
	require_once BASE_DIR . '/lib/class-autoloader.php';
}

// Check for BC credentials to be defined.
if ( ! defined( 'BC_CLIENT_ID' ) || ! defined( 'BC_CLIENT_SECRET' ) ) {
	echo 'BC_CLIENT_ID or BC_CLIENT_SECRET not defined. Define these in a config file in the document root.';
	exit( 0 );
}