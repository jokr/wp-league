<?php

require_once dirname( __FILE__ ) . '/class-admin-screen.php';
require_once dirname( __FILE__ ) . '/class-tournament-screen.php';
require_once dirname( __FILE__ ) . '/class-player-screen.php';

class League_Screen extends Admin_Screen
{
	private $leagues;

	public function __construct( League_Plugin $plugin ) {
		parent::__construct();
		$this->leagues = $plugin->get_leagues();

		add_action( 'admin_post_add_league', array($this, 'add_league') );

		new Tournament_Screen(
			$plugin->get_tournaments(),
			$plugin->get_leagues(),
			$plugin->get_players(),
			$plugin->get_matches()
		);
		new Player_Screen();
	}

	public function add_admin_menu() {
		add_admin_menu_separator( 26 );
		add_menu_page(
			'Leagues',
			'Leagues',
			'publish_pages',
			'leagues',
			array($this, 'load_league_menu'),
			'dashicons-star-empty',
			30
		);
		add_admin_menu_separator( 31 );
	}

	public function load_league_menu() {
		load_template( LEAGUE_PLUGIN_DIR . '/templates/league-admin.php' );
	}

	public function ajax_callbacks() {
		add_action( 'wp_ajax_get_league', array($this, 'ajax_get_league') );
	}

	public function ajax_get_league() {
		if ( isset($_POST['league']) && is_numeric( $_POST['league'] ) ) {
			$result = $this->leagues->get_by_id( $_POST['league'] );
			header( 'Content-Type: application/json' );
			echo json_encode( $result );
		} else {
			http_response_code( 406 );
			echo "No league id sent or it is non numeric.";
		}
		die();
	}

	public function add_league() {
		if ( ! current_user_can( 'publish_pages' ) ) {
			wp_die( 'You do not have sufficient permissions to access this page.' );
		}

		check_admin_referer( 'add-league', '_wpnonce_add_league' );

		if ( isset($_POST['league']) ) {
			$league = new League(array(
				'name' => sanitize_text_field( $_POST['league']['name'] ),
				'start' => sanitize_text_field( $_POST['league']['start'] ),
				'end' => sanitize_text_field( $_POST['league']['end'] )
			));
			$this->leagues->save( $league );
		}

		wp_redirect( add_query_arg( 'updated', 'true', admin_url( 'admin.php?page=leagues' ) ) );
	}
}