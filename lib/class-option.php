<?php
/**
 * Handles management of options in the database.
 *
 * @package Basecamp RSS Feed Parser
 */

/**
 * Options class.
 */
class Option {
	/**
	 * Update option
	 *
	 * @param  string $key    Option name.
	 * @param  mixed  $value  Option value.
	 * @return int            Returns the number of affected rows
	 */
	public static function update( $key, $value ) {
		// Serialize value if not a string.
		if ( ! is_string( $value ) && ! is_numeric( $value ) ) {
			$value = serialize( $value );
		}

		// Prepare query.
		$query = DB::prepare( "
			UPDATE 
				`options` 
			SET 
				`option_value` = :value
			WHERE 
				`option_name` = :name
		" ); // @codingStandardsIgnoreLine

		// Bind params.
		$query->bindValue( ':name', $key );
		$query->bindValue( ':value', $value );

		return $query->execute();
	}

	/**
	 * Gets option
	 *
	 * @param  string $key   		 Option name.
	 * @param  bool   $return_single Return single value of array.
	 * @return mixed         		 Option value.
	 */
	public static function get( $key, $return_single = true ) {
		// Prepare query.
		$query = DB::prepare( "
			SELECT 
				`option_value` 
			FROM 
				`options`
			WHERE 
				`option_name` = :name
		" );

		// Bind params.
		$query->bindValue( ':name', $key );
		$query->execute();

		$results = $query->fetchAll();

		// Bail early if no results.
		if ( empty( $results ) ) {
			return false;
		}

		return $return_single ? Utilities::maybe_unserialize( $results[0]['option_value'] ) : $results;
	}
}
