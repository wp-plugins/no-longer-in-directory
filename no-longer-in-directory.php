<?php
/*
Plugin Name: No Longer in Directory
Plugin URI: http://www.whitefirdesign.com/no-longer-in-directory
Description: Checks for installed plugins that are no longer in the WordPress.org Plugin Directory.
Version: 1.0.29
Author: White Fir Design
Author URI: http://www.whitefirdesign.com/
License: GPLv2
Text Domain: no-longer-in-directory
Domain Path: /languages

Copyright 2012-2014 White Fir Design

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; only version 2 of the License is applicable.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//Block direct access to the file
if ( !function_exists( 'add_action' ) ) { 
	exit; 
} 

function no_longer_in_directory_init() {
	load_plugin_textdomain( 'no-longer-in-directory', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action('init', 'no_longer_in_directory_init');


function no_longer_in_directory_add_pages() {
	add_plugins_page( 'No Longer in Directory', 'No Longer in Directory', 'manage_options', 'no-longer-in-directory', 'no_longer_in_directory_page'  );
}
add_action('admin_menu', 'no_longer_in_directory_add_pages');

function no_longer_in_directory_page() {

	$plugin_list = get_plugins();
	$plugin_list_paths = array_keys($plugin_list);
	$plugin_path;
	$no_longer_in_directory = array();
	$disappeared_plugins = file(dirname( __FILE__ ) . '/no-longer-in-directory-plugin-list.txt', FILE_IGNORE_NEW_LINES);
	$two_year_plugins = file(dirname( __FILE__ ) . '/not-updated-in-over-two-years-plugin-list.txt', FILE_IGNORE_NEW_LINES);
	
	//Clean array elements of extraneous characters
	$disappeared_plugins = array_map( 'trim', $disappeared_plugins );

	//Check for installed plugins that are no longer in the WordPress.org Plugin Directory
	foreach ( $plugin_list_paths as &$value ) {
		preg_match_all('/([a-z0-9\-]+)\//', $value, $plugin_path);
		if ( in_array ($plugin_path[1][0], $disappeared_plugins )) {
			//Check that plugin has not returned to the WordPress.org Plugin Directory since plugin list last generated
			$directory_plugin_head = wp_remote_head('http://wordpress.org/plugins/'.$plugin_path[1][0].'/');
			if ( $directory_plugin_head ['response']['code'] == "404" )
				$no_longer_in_directory[$plugin_list[$value]['Name']]= $plugin_path[1][0];
		}
		else if ( in_array ($plugin_path[1][0], $two_year_plugins )) {
			//Check that plugin has not been updated in the WordPress.org Plugin Directory since plugin list last generated
			$directory_plugin_get = wp_remote_get('http://wordpress.org/plugins/'.$plugin_path[1][0].'/', array('body'));
			if ( strpos($directory_plugin_get[body], "It may no longer be maintained or supported and may have compatibility issues when used with more recent versions of WordPress"))
				$not_updated_in_over_two_years[$plugin_list[$value]['Name']]= $plugin_path[1][0];
		}
	}

	//Generate page
	echo '<div class="wrap">';
	echo '<h2>No Longer in Directory</h2>';
	if ( !empty($no_longer_in_directory)  ) {			
		//Load Secunia advisories
		$secunia_file = fopen(dirname( __FILE__ ) . '/secunia-advisories.txt', "r");
		$secunia_advisories = array();
		while (!feof($secunia_file) ) { 
			$line = fgetcsv($secunia_file, 1024, ","); 
			$secunia_advisories[$line[0]] = $line[1]; 
		}

		echo "<h3>".__('Installed plugins that are no longer in the WordPress.org Plugin Directory:', 'no-longer-in-directory')."</h3>";
		echo "<p>";
		foreach ( $no_longer_in_directory as $plugin_name => &$plugin_stub ) {
			echo $plugin_name;

			//Check for Secunia advisory
			if (array_key_exists($plugin_stub, $secunia_advisories))
				echo ' (<a href="'.$secunia_advisories[$plugin_stub].'" target="_blank">Secunia Advisory</a>)';
				
			echo '</li><br />';
		}
		echo "</p>";
	}
	else 
		echo "<h3>".__('No installed plugins are no longer in the WordPress.org Plugin Directory.', 'no-longer-in-directory')."</h3>";
	echo "<br><br>";
	if ( !empty($not_updated_in_over_two_years) ) {

		echo "<h3>".__('Installed plugins that have not been updated for over two years in the WordPress.org Plugin Directory:', 'no-longer-in-directory')."</h3>";
		echo "<p>";
		foreach ( $not_updated_in_over_two_years as $plugin_name => &$plugin_stub ) {
			echo $plugin_name;
			echo '</li><br />';
		}
		echo "</p>";
	}
	else 
		echo "<h3>".__('No installed plugins were last updated over two years ago in the WordPress.org Plugin Directory.', 'no-longer-in-directory')."</h3>";
	echo '</div>';
}