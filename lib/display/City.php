<?php
/**
 * City Details Information class
 *
 * This class is used to fetch data from the MySQL database for 
 * information regarding cities. Some of the capibilities of
 * this class includes:
 *  - Fetch a listing of needed rides cities leaving a particular
 *    city.
 *  - Fetch a listing of available rides cities leaving a particular
 *    city.
 *  - Fetch a listing of cities with rides in a state.
 *  - Purify a string for use in a URL.
 * 
 * @author    Oliver Spryn
 * @copyright Copyright (c) 2013 and Onwards, ForwardFour Innovations
 * @license   MIT
 * @namespace FFI\TA
 * @package   lib.display
 * @since     1.0.0
*/

namespace FFI\TA;

require_once(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . "/wp-blog-header.php");

class City {
/**
 * This function will return a listing of destination cities with
 * trips in need of a ride to a particular origin city.
 *
 * @access public
 * @param  string $cityURL   The URL of the origin city
 * @param  string $stateCode The code of the origin state
 * @return object            A list of destination cities which have at least one requested ride
 * @since  1.0.0
 * @static
*/
	
	public static function getDestinationNeedCities($cityURL, $stateCode) {
		global $wpdb;
		
		return $wpdb->get_results($wpdb->prepare("SELECT q.ID, `Requestee`, `Leaving`, `LeavingTimeZone`, `Occupants`, `FromCity`, `FromState`, `FromLatitude`, `FromLongitude`, `City` AS `ToCity`, `State` AS `ToState`, `Latitude` AS `ToLatitude`, `Longitude` AS `ToLongitude` FROM (SELECT ffi_ta_need.ID, `Requestee`, `Leaving`, `LeavingTimeZone`, `MalesPresent` + `FemalesPresent` + 1 AS `Occupants`, `City` AS `FromCity`, `State` AS `FromState`, `Latitude` AS `FromLatitude`, `Longitude` AS `FromLongitude`, `ToCity` FROM  `ffi_ta_need` LEFT JOIN `ffi_ta_cities` ON ffi_ta_need.FromCity = ffi_ta_cities.ID LEFT JOIN (SELECT wp_usermeta.user_id AS `ID`, CONCAT(wp_usermeta.meta_value, ' ', last.meta_value) AS `Requestee` FROM `wp_usermeta` LEFT JOIN (SELECT `meta_value`, `user_id` FROM `wp_usermeta` WHERE `meta_key` = 'last_name') AS `last` ON wp_usermeta.user_id = last.user_id  WHERE `meta_key` = 'first_name') AS `users` ON ffi_ta_need.Person = users.ID WHERE REPLACE(LOWER(ffi_ta_cities.City), ' ', '-') = %s AND ffi_ta_cities.State = %s AND (ffi_ta_need.Leaving > NOW() AND ffi_ta_need.Fulfilled = 0) OR (ffi_ta_need.Leaving <= NOW() AND ffi_ta_need.EndDate > NOW() AND ffi_ta_need.Fulfilled = 0) ORDER BY `Leaving` ASC) `q` LEFT JOIN `ffi_ta_cities` ON q.ToCity = ffi_ta_cities.ID ORDER BY `ToCity` ASC, `ToState` ASC, `Leaving` ASC", $cityURL, $stateCode));
	}
	
/**
 * This function will return a listing of destination cities with
 * trips sharing a ride to a particular origin city.
 *
 * @access public
 * @param  string $cityURL   The URL of the origin city
 * @param  string $stateCode The code of the origin state
 * @return object            A list of destination cities which have at least one available ride
 * @since  1.0.0
 * @static
*/
	
	public static function getDestinationShareCities($cityURL, $stateCode) {
		global $wpdb;
		
		return $wpdb->get_results($wpdb->prepare("SELECT q.ID, `Requestee`, `Leaving`, `LeavingTimeZone`, `Seats`, `FromCity`, `FromState`, `FromLatitude`, `FromLongitude`, `City` AS `ToCity`, `State` AS `ToState`, `Latitude` AS `ToLatitude`, `Longitude` AS `ToLongitude` FROM (SELECT ffi_ta_share.ID, `Requestee`, `Leaving`, `LeavingTimeZone`, `Seats`, `City` AS `FromCity`, `State` AS `FromState`, `Latitude` AS `FromLatitude`, `Longitude` AS `FromLongitude`, `ToCity` FROM  `ffi_ta_share` LEFT JOIN `ffi_ta_cities` ON ffi_ta_share.FromCity = ffi_ta_cities.ID LEFT JOIN (SELECT wp_usermeta.user_id AS `ID`, CONCAT(wp_usermeta.meta_value, ' ', last.meta_value) AS `Requestee` FROM `wp_usermeta` LEFT JOIN (SELECT `meta_value`, `user_id` FROM `wp_usermeta` WHERE `meta_key` = 'last_name') AS `last` ON wp_usermeta.user_id = last.user_id  WHERE `meta_key` = 'first_name') AS `users` ON ffi_ta_share.Person = users.ID WHERE REPLACE(LOWER(ffi_ta_cities.City), ' ', '-') = %s AND ffi_ta_cities.State = %s AND (ffi_ta_share.Leaving > NOW() AND ffi_ta_share.Seats > ffi_ta_share.Fulfilled) OR (ffi_ta_share.Leaving <= NOW() AND ffi_ta_share.EndDate > NOW() AND ffi_ta_share.Seats > ffi_ta_share.Fulfilled) ORDER BY `Leaving` ASC) `q` LEFT JOIN `ffi_ta_cities` ON q.ToCity = ffi_ta_cities.ID ORDER BY `ToCity` ASC, `ToState` ASC, `Leaving` ASC", $cityURL, $stateCode));
	}
	
/**
 * This function will return a listing of origin cities sorted
 * alphabetically with the number of rides needed or available,
 * when given the URL of the desired state.
 *
 * @access public
 * @param  string $stateURL The URL of the state in which to fetch the listing of cities
 * @return object           A list of origin cities with available or needed trips
 * @since  1.0.0
 * @static
*/

	public static function getOriginCities($stateURL) {
		global $wpdb;

		return $wpdb->get_results($wpdb->prepare("SELECT `City`, `StateName`, `Code`, REPLACE(LOWER(`StateName`), ' ', '-') AS `URL`, `Image`, `District`, `Latitude`, `Longitude`, `Needs`, `Shares` FROM (SELECT ffi_ta_cities.*, ffi_ta_states.Name AS `StateName`, ffi_ta_states.Code, ffi_ta_states.Image, ffi_ta_states.District, COALESCE(q1.Needs, 0) AS `Needs`, COALESCE(q2.Shares, 0) AS `Shares` FROM `ffi_ta_cities` LEFT JOIN `ffi_ta_states` ON ffi_ta_cities.State = ffi_ta_states.Code LEFT JOIN (SELECT `FromCity`, COUNT(`FromCity`) AS `Needs` FROM `ffi_ta_need` WHERE (ffi_ta_need.Leaving > NOW() AND ffi_ta_need.Fulfilled = 0) OR (ffi_ta_need.Leaving <= NOW() AND ffi_ta_need.EndDate > NOW() AND ffi_ta_need.Fulfilled = 0) GROUP BY `FromCity`) `q1` ON ffi_ta_cities.ID = q1.FromCity LEFT JOIN (SELECT `FromCity`, COUNT(`FromCity`) AS `Shares` FROM `ffi_ta_share` WHERE (ffi_ta_share.Leaving > NOW() AND ffi_ta_share.Seats > ffi_ta_share.Fulfilled) OR (ffi_ta_share.Leaving <= NOW() AND ffi_ta_share.EndDate > NOW() AND ffi_ta_share.Seats > ffi_ta_share.Fulfilled) GROUP BY `FromCity`) `q2` ON ffi_ta_cities.ID = q2.FromCity) `query` WHERE `Needs` > 0 OR `Shares` > 0 HAVING `URL` = %s ORDER BY `City` ASC", $stateURL));
	}
	
/**
 * This function will take a string and prepare it for use in a
 * URL by removing any spaces and special characters, and then 
 * making all characters lower case, which is this plugin's
 * convention when placing strings in a URL.
 * 
 * @access public
 * @param  string $name The name of a state
 * @return string       The URL purified version of the string
 * @since  1.0.0
 * @static
*/
	public static function URLPurify($name) {
		$name = preg_replace("/[^a-zA-Z0-9\s\-]/", "", $name); //Remove all non-alphanumeric characters, except for spaces
		$name = preg_replace("/[\s]/", "-", $name);            //Replace remaining spaces with a "-"
		$name = str_replace("--", "-", $name);                 //Replace "--" with "-", will occur if a something like " & " is removed
		return strtolower($name);
	}
}
?>