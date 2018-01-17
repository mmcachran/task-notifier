<?php
/**
 * Holds the autoloader for this app.
 *
 * @package  Basecamp RSS Feed Parser
 */

/**
 * Class to handle autoloading files.
 */
class Autoloader {

	/**
	 * Autoloads files with classes when needed
	 *
	 * @since  0.1.0
	 * @param  string $class_name Name of the class being requested.
	 * @throws \Exception Throws an exception if the class file doesn't exist.
	 * @return  null
	 */
	public static function autoload_classes( $class_name ) {
		// Bail early if class already exists.
		if ( class_exists( $class_name ) ) {
			return;
		}

		// Get the file parts.
		$file_parts       = explode( '\\', $class_name );
		$file_parts_count = count( $file_parts );

		// Determine by namespace how to include files.
		if ( 1 < $file_parts_count ) {
			// Default namespace to null until we can fill it in.
			$namespace = '';

			// Do a reverse loop through $file_parts to build the path to the file.
			for ( $i = $file_parts_count - 1; $i >= 0; --$i ) {
				// Read the current component of the file part.
				$current = strtolower( $file_parts[ $i ] );
				$current = str_ireplace( '_', '-', $current );

				// If we're at the first entry, then we're at the filename.
				if ( $file_parts_count - 1 === $i ) {
					$filename = "class-{$current}.php";
				} else {
					$namespace = $current . '/' . $namespace;
				}
			}

			// Determine what the full filepath is.
			$filepath = BASE_DIR . '/lib/' . $namespace . '/' . $filename;

		} else {
			// Determine the filename.
			$filename = strtolower( str_ireplace(
				array( '_' ),
				array( '-' ),
				$class_name
			) );

			// Determine the full file path.
			$filepath = BASE_DIR . '/lib/class-' . $filename . '.php';
		} // End if().

		// Throw error if the class file doesn't exist.
		if ( ! file_exists( $filepath ) ) {
			throw new \Exception( $filepath . ' does not exist.' );
		}

		// Include the file.
		include_once $filepath;
	}
}
spl_autoload_register( 'Autoloader::autoload_classes' );
