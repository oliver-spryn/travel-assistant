<?php
/**
 * Travel Assistant Administration Display class
 *
 * This class is responsible for fetching data for the main display of 
 * the Travel Assistant administration in the Wordpress Administration
 * section of the site. Some of its duties includes:
 *  - Fetch all of the plugin's settings.
 *  - Fetch all of the APIs which the plugin uses.
 * 
 * @author    Oliver Spryn
 * @copyright Copyright (c) 2013 and Onwards, ForwardFour Innovations
 * @license   MIT
 * @namespace FFI\TA
 * @package   lib.display
 * @since     1.0
*/

namespace FFI\TA;

require_once(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . "/wp-blog-header.php");

class Admin {
	public static function APIData() {
		global $wpdb;
		
		$data = $wpdb->get_results("SELECT * FROM `ffi_ta_apis` WHERE `ID` = '1'");
		return $data[0];
	}
	
	public static function settings() {
		global $wpdb;
		
		$data = $wpdb->get_results("SELECT * FROM `ffi_ta_settings` WHERE `ID` = '1'");
		return $data[0];
	}
}
?>