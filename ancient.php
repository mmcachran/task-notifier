<?php
/**
 * Template to parse old tasks that haven't been updated recently.
 *
 * @package Basecamp RSS Feed Parser
 */

 // Sets the default timezone.
date_default_timezone_set( 'America/New_York' ); // @codingStandardsIgnoreLine

// Set a constant for the base directory path.
define( 'BASE_DIR', realpath( dirname( __FILE__ ) ) );

// Include config file.
if ( file_exists( BASE_DIR . '/config.php' ) ) {
	require_once BASE_DIR . '/config.php';
}

// Include autoloader file.
if ( file_exists( BASE_DIR . '/lib/class-autoloader.php' ) ) {
	require_once BASE_DIR . '/lib/class-autoloader.php';
}

// Initialize the BC old tasks class.
$basecamp = \Basecamp\Basecamp_Old_Tasks::get_instance();

// Set some properties for OAuth.
$basecamp->set_bc_id( BC_ID );
$basecamp->set_client_id( BC_CLIENT_ID );
$basecamp->set_client_secret( BC_CLIENT_SECRET );
$basecamp->set_redirect_uri( BC_REDIRECT_URI );

// Fetch old tasks.
$project_tasks = $basecamp->get_old_tasks();
?>

<!DOCTYPE html>
<html>
	<head></head>
	<body>
		<?php
		// @codingStandardsIgnoreStart
		echo '<pre>';
		var_dump( $project_tasks );
		echo '</pre>';
		// @codingStandardsIgnoreEnd
		?>
	</body>
</html>
