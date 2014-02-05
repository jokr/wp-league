<?php

abstract class Admin_Screen
{
	protected $plugin;

	public function __construct() {
		add_action( 'admin_menu', array($this, 'add_admin_menu') );
		add_action( 'admin_init', array($this, 'admin_init') );
	}

	public function admin_init() {
		$this->register_resources();
		$this->ajax_callbacks();
	}

	public abstract function add_admin_menu();

	protected function current_action() {
		if (isset( $_REQUEST['action'] ) && - 1 != $_REQUEST['action'])
			return $_REQUEST['action'];
		return false;
	}

	protected function register_resources() {
		wp_register_script(
			'jquery-ui-timepicker',
			LEAGUE_PLUGIN_URL . 'js/jquery.timepicker.min.js',
			array(),
			LEAGUE_PLUGIN_VERSION
		);

		wp_register_script(
			'league-admin',
			LEAGUE_PLUGIN_URL . 'js/league-admin.js',
			array('jquery-ui-datepicker'),
			LEAGUE_PLUGIN_VERSION,
			true
		);

		wp_register_script(
			'tournament-admin',
			LEAGUE_PLUGIN_URL . 'js/tournament-admin.js',
			array('jquery-ui-datepicker', 'jquery-ui-timepicker'),
			LEAGUE_PLUGIN_VERSION,
			true
		);

		wp_register_style(
			'league-admin-jquery-ui',
			get_server_protocol() . 'code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css',
			array(),
			LEAGUE_PLUGIN_VERSION
		);

		wp_register_style(
			'jquery-timepicker',
			LEAGUE_PLUGIN_URL . 'css/jquery.timepicker.css',
			array(),
			LEAGUE_PLUGIN_VERSION
		);

		wp_register_style(
			'league-admin',
			LEAGUE_PLUGIN_URL . 'css/league-admin.css',
			array('league-admin-jquery-ui'),
			LEAGUE_PLUGIN_VERSION
		);

		wp_register_style(
			'tournament-admin',
			LEAGUE_PLUGIN_URL . 'css/tournament-admin.css',
			array('league-admin-jquery-ui', 'jquery-timepicker'),
			LEAGUE_PLUGIN_VERSION
		);
	}

	protected function ajax_callbacks(){
		return;
	}
}