<?php

function add_admin_menu_separator( $position ) {
	global $menu;
	$index = 0;
	foreach ( $menu as $offset => $section ) {
		if ( substr( $section[2], 0, 9 ) == 'separator' )
			$index ++;
		if ( $offset >= $position ) {
			$menu[$position] = array( '', 'read', "separator{$index}", '', 'wp-menu-separator' );
			break;
		}
	}
	ksort( $menu );
}

if ( ! function_exists( 'get_server_protocol' ) ) {
	function get_server_protocol() {
		return is_ssl() ? 'https://' : 'https://';
	}
}