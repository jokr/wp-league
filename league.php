<?php
/*
Plugin Name: League
Plugin URI: http://www.aleaiactaest.ch
Description: Functionality to create and manage leagues for a game.
Version: 0.1
Author: Joel Krebs
Author URI: http://www.aleaiactaest.ch
License: GPL2
*/

include_once dirname( __FILE__ ) . '/includes/class-league-plugin.php';
include_once dirname( __FILE__ ) . '/functions.php';

if ( class_exists( 'League_Plugin' ) ) {
	define('LEAGUE_PLUGIN_DIR', plugin_dir_path( __FILE__ ));
	define('LEAGUE_PLUGIN_URL', plugin_dir_url( __FILE__ ));
	define('LEAGUE_PLUGIN_VERSION', '0.1');

    $league_plugin = new League_Plugin();

	register_activation_hook( __FILE__, array($league_plugin, 'activate') );
	register_deactivation_hook( __FILE__, array($league_plugin, 'deactivate') );
}

