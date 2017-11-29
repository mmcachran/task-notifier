<?php
/**
 * Holds the autoloader for this app.
 *
 * @package  Basecamp RSS Feed Parser
 */

/**
 * Autoloads files with classes when needed
 *
 * @since  0.1.0
 * @param  string $class_name Name of the class being requested.
 * @throws \Exception Throws an exception if the class file doesn't exist.
 * @return  null
 */
function wds_autoload_classes( $class_name ) {
	// Bail early if class already exists.
	if ( class_exists( $class_name ) ) {
		return;
	}

	// Determine the filename.
	$filename = strtolower( str_ireplace(
		array( '_' ),
		array( '-' ),
		$class_name
	) );

	// Determine the full file path.
	$file = BASE_DIR . '/lib/class-' . $filename . '.php';

	// Throw error if the class file doesn't exist.
	if ( ! file_exists( $file ) ) {
		throw new \Exception( $file . ' does not exist.' );
	}

	// Include the file.
	include_once( $file );
}
spl_autoload_register( 'wds_autoload_classes' );
