<?php
/**
 * Time Class
 *
 * @package Basecamp RSS Feed Parser
 */

namespace Utilities;

/**
 * Class to hold time utilities.
 */
class Time {
	/**
	 * Get the class to add to the table row based on days since last update.
	 *
	 * @param  int $diff Days since the last update.
	 * @return string    Class to add to table row.
	 */
	public static function get_time_diff_class( $diff ) {
		switch ( true ) {
			case ( (int) $diff > 3 ):
				return 'red';

			case ( (int) $diff > 1 ):
				return 'yellow';

			default:
				return '';
		}
	}
}
