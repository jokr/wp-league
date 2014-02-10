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

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
} // end if

define( 'LEAGUE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'LEAGUE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'LEAGUE_PLUGIN_VERSION', '0.1' );

include_once dirname( __FILE__ ) . '/includes/class-league-plugin.php';
include_once dirname( __FILE__ ) . '/functions.php';
add_action( 'plugins_loaded', array('League_Plugin', 'get_instance') );