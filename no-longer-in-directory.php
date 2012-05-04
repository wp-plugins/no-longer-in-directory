<?php
/*
Plugin Name: No Longer in Directory
Plugin URI: http://www.whitefirdesign.com/no-longer-in-directory
Description: Checks for installed plugins that are no longer in the WordPress.org Plugin Directory.
Version: 1.0.4
Author: White Fir Design
Author URI: http://www.whitefirdesign.com/
License: GPLv2
Text Domain: no-longer-in-directory
Domain Path: /languages

Copyright 2012 White Fir Design

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
	$disappeared_plugins = file(dirname( __FILE__ ) . '/plugin-list.php', FILE_IGNORE_NEW_LINES);
	
	//Clean array elements of extraneous characters
	$disappeared_plugins = array_map( 'trim', $disappeared_plugins );

	//Check for installed plugins that are no longer in the WordPress.org Plugin Directory
	foreach ( $plugin_list_paths as &$value ) {
		preg_match_all('/([a-z0-9\-]+)\//', $value, $plugin_path);
		if ( in_array ($plugin_path[1][0], $disappeared_plugins )) {
			//Check that plugin has not returned to the WordPress.org Plugin Directory since plugin list last generated
			$directory_plugin_head = wp_remote_head('http://wordpress.org/extend/plugins/'.$plugin_path[1][0].'/');
			if ( $directory_plugin_head ['response']['code'] == "404" )
				$no_longer_in_directory[$plugin_list[$value]['Name']]= $plugin_path[1][0];
		}
	}

	//Generate page
	echo '<div class="wrap">';
	echo '<div id="icon-plugins" class="icon32"><br /></div>';	
	echo '<h2>No Longer in Directory</h2><p>';
	if ( !empty($no_longer_in_directory) ) {
		
		//Load Secunia advisories
		$secunia_file = fopen(dirname( __FILE__ ) . '/secunia-advisories.php', "r");
		$secunia_advisories = array();
		while (!feof($secunia_file) ) { 
			$line = fgetcsv($secunia_file, 1024, ","); 
			$secunia_advisories[$line[0]] = $line[1]; 
		}

		_e('Installed plugins that are no longer in the WordPress.org Plugin Directory:', 'no-longer-in-directory');
		echo '<br />';
		foreach ( $no_longer_in_directory as $plugin_name => &$plugin_stub ) {
			echo $plugin_name;

			//Check for Secunia advisory
			if (array_key_exists($plugin_stub, $secunia_advisories))
				echo ' (<a href="'.$secunia_advisories[$plugin_stub].'" target="_blank">Secunia Advisory</a>)';
			
			echo '</li><br />';
		}	
	}
	else 
		_e('No installed plugins are no longer in the WordPress.org Plugin Directory.', 'no-longer-in-directory');
	echo '</p></div>';
}