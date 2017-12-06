<?php
/**
 * Basecamp class for "old" tasks
 *
 * @package  Basecamp RSS Feed Parser
 */

namespace Basecamp;

/**
 * Class to handle Basecamp RSS feed parsing.
 */
class Basecamp_Old_Tasks extends \Basecamp\Base {
	/**
	 * Holds single instance of this class
	 *
	 * @var Basecamp_Old_Tasks
	 */
	protected static $instance = null;

	/**
	 * Include tasks not updated in this amount of days.
	 *
	 * @var  integer
	 */
	const DAY_THRESHOLD = -1;

	/**
	 * Old tasks names to exclude.
	 *
	 * @var array
	 */
	protected $old_tasks_list_excluded_task_names = array();

	/**
	 * Excluded todo list IDs for the old tasks list.
	 *
	 * @var array
	 */
	protected $old_tasks_list_excluded_todo_list_ids = array();

	/**
	 * Excluded project IDs for the old tasks list.
	 *
	 * @var array
	 */
	protected $old_tasks_list_excluded_project_ids = array();

	/**
	 * Returns a single instance of the class
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Get all old tasks not updated recently.
	 *
	 * @return array Old tasks.
	 */
	public function get_old_tasks() {
		// Holds our response.
		$response = array();

		// Get all projects.
		$projects = $this->get_projects();

		// Bail early if no projects.
		if ( empty( $projects ) ) {
			return $response;
		}

		// Loop through projects and get old tasks.
		foreach ( (array) $projects as $project ) {
			// Skip if project is exluded.
			if ( in_array( $project->id, $this->old_tasks_list_excluded_project_ids ) ) { // @codingStandardsIgnoreLine
				continue;
			}

			// Add to our list.
			$response[ $project->name ] = $this->get_old_tasks_from_project( $project->id );
		}

		return $response;
	}

	/**
	 * Gets tasks not updated in a while for a project.
	 *
	 * @param  string $project_id The project's ID.
	 * @return array              Old tasks for the project.
	 */
	protected function get_old_tasks_from_project( $project_id ) {
		// Holds response.
		$response  = array();

		// Build the request URL.
		$tasks_url = 'https://basecamp.com/' . BC_ID . '/api/v1/projects/' . $project_id . '/todos/remaining.json';

		$auth_args = array(
			'token' => $this->oauth_tokens->access_token,
		);

		// Get remaining tasks for the project.
		$tasks = \Request::get( $tasks_url, $auth_args );

		// Bail early if no tasks.
		if ( empty( $tasks ) ) {
			return $response;
		}

		// Loop through tasks to find old ones.
		foreach ( (array) $tasks as $task ) {
			// Skip if update was recent.
			if ( strtotime( $task->updated_at ) > strtotime( self::DAY_THRESHOLD . ' days' ) ) {
				continue;
			}

			// Skip if in the list of ignored names.
			if ( in_array( $task->content, $this->old_tasks_list_excluded_task_names ) ) { // @codingStandardsIgnoreLine
				continue;
			}

			// Skip if in the list of exluded todo lists.
			if ( in_array( $task->todolist_id, $this->old_tasks_list_excluded_todo_list_ids ) ) { // @codingStandardsIgnoreLine
				continue;
			}

			// Add it to our list.
			$response[] = $task;
		}

		return $response;
	}
}
