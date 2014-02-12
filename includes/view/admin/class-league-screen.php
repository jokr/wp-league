<?php

require_once dirname( __FILE__ ) . '/class-admin-screen.php';

class League_Screen extends Admin_Screen
{
	private $leagues;
	private $tournaments;

	public function __construct( League_Service $leagues, Tournament_Service $tournaments ) {
		parent::__construct();
		$this->leagues = $leagues;
		$this->tournaments = $tournaments;

		add_action( 'admin_post_add_league', array($this, 'add_league') );
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
        wp_enqueue_script( 'league-admin' );
        wp_enqueue_style( 'league-admin' );
        require_once LEAGUE_PLUGIN_DIR . 'includes/view/admin/class-leagues-list-table.php';
        $league_list = new Leagues_List_Table($this->leagues, $this->tournaments);
        $league_list->prepare_items();
		include_once LEAGUE_PLUGIN_DIR . 'templates/league-admin.php';
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
            $this->leagues->add_league(
                sanitize_text_field( $_POST['league']['name'] ),
                sanitize_text_field( $_POST['league']['start'] ),
                sanitize_text_field( $_POST['league']['end'] )
            );
		}

		wp_redirect( add_query_arg( 'updated', 'true', admin_url( 'admin.php?page=leagues' ) ) );
	}
}