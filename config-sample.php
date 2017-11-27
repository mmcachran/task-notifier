<?php
/**
 * DB credentials.
 *
 * @package Basecamp RSS Feed Parser
 */

/**
 * Database Credentials.
 */
if ( ! defined( 'DB_HOST' ) ) {
	define( 'DB_HOST', '' );
}

if ( ! defined( 'DB_USER' ) ) {
	define( 'DB_USER', '' );
}

if ( ! defined( 'DB_PASS' ) ) {
	define( 'DB_PASS', '' );
}

if ( ! defined( 'DB_NAME' ) ) {
	define( 'DB_NAME', '' );
}

/**
 * BC API credentials
 */
if ( ! defined( 'BC_ID' ) ) {
	define( 'BC_ID', '' );
}

if ( ! defined( 'BC_CLIENT_ID' ) ) {
	define( 'BC_CLIENT_ID', '' );
}

if ( ! defined( 'BC_CLIENT_SECRET' ) ) {
	define( 'BC_CLIENT_SECRET', '' );
}

if ( ! defined( 'BC_REDIRECT_URI' ) ) {
	define( 'BC_REDIRECT_URI', '' ); // This is usually the path to cron.php.
}

/**
 * HelpScout API
 */
if ( ! defined( 'HS_API_KEY' ) ) {
	define( 'HS_API_KEY', '' );
}

if ( ! defined( 'HS_MAILBOX_ID' ) ) {
	define( 'HS_MAILBOX_ID', '' );
}

if ( ! defined( 'HS_CUSTOMER_EMAIL' ) ) {
	define( 'HS_CUSTOMER_EMAIL', '' );
}
