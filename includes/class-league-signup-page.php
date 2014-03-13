<?php

require_once dirname( __FILE__ ) . '/class-tomjn-custom-page.php';

class League_Signup_Page extends Tomjn_Custom_Page
{

	/**
	 * Displays the content, extend this class and implement this function as needed
	 */
	public function render_page() {
		load_template( LEAGUE_PLUGIN_DIR . 'templates/league-signup.php' );
	}
}