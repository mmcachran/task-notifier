<?php
/**
 * Template to parse old tasks that haven't been updated recently.
 *
 * @package Basecamp RSS Feed Parser
 */

date_default_timezone_set( 'America/New_York' );

define( 'BASE_DIR', realpath( dirname( __FILE__ ) ) );

// Include config file.
if ( file_exists( BASE_DIR . '/config.php' ) ) {
	require_once( BASE_DIR . '/config.php' );
}

// Include autoloader file.
if ( file_exists( BASE_DIR . '/lib/class-autoloader.php' ) ) {
	require_once( BASE_DIR . '/lib/class-autoloader.php' );
}

// Fetch old tasks.
$basecamp = \Basecamp\Basecamp_Old_Tasks::get_instance();
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
