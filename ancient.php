<?php
/**
 * Template to parse old tasks that haven't been updated recently.
 *
 * @package Basecamp RSS Feed Parser
 */

 // Set a constant for the base directory path.
define( 'BASE_DIR', realpath( dirname( __FILE__ ) ) );

// Include config file.
if ( file_exists( BASE_DIR . '/loader.php' ) ) {
	require_once BASE_DIR . '/loader.php';
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
	<head>
		<link rel="stylesheet" href="assets/css/styles.css" /> <?php // @codingStandardsIgnoreLine ?>
		<script src="assets/js/vendor/jquery.min.js" type="text/javascript"> <?php // @codingStandardsIgnoreLine ?></script>
		<script src="assets/js/ancient.min.js" type="text/javascript"> <?php // @codingStandardsIgnoreLine ?></script>
	</head>
	<body>
		<div id="search-wrapper">
			<input class="search" name="q" />
			<label>
				Search
			</label>
		</div>
		<?php
		foreach ( $project_tasks as $project => $tasks ) :
			// Skip if no tasks.
			if ( empty( $tasks ) ) {
				continue;
			}
			?>
			<div class="project">
				<h2 class="project-title">
					<?php echo $project; // @codingStandardsIgnoreLine ?>
				</h2>
				<div class="table">
					<div class="th">
						<div class="td">Task</div>
						<div class="td">Assignee</div>
						<div class="td">Due Date</div>
						<div class="td">Last Update</div>
						<div class="clear"></div>
					</div>

					<?php foreach ( $tasks as $task ) : ?>
						<div class="tr">
							<div class="td">
								<?php echo $task->content; // @codingStandardsIgnoreLine ?>
							</div>
							<div class="td">
								<?php isset( $task->assignee->name ) ? $task->assignee->name : ''; // @codingStandardsIgnoreLine ?>
							</div>
							<div class="td">
								<?php echo $task->due_on; // @codingStandardsIgnoreLine ?>
							</div>
							<div class="td">
								<?php echo date( 'Y-m-d', strtotime( $task->updated_at ) ); // @codingStandardsIgnoreLine ?>
							</div>
							<div style="clear: both;"></div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<?php
		endforeach;
		?>
	</body>
</html>
