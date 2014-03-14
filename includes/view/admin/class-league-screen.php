<?php

require_once dirname( __FILE__ ) . '/class-admin-screen.php';

class League_Screen extends Admin_Screen
{
	private $leagues;
	private $tournaments;

	private $slug;

	public function __construct( League_Service $leagues, Tournament_Service $tournaments ) {
		parent::__construct();
		$this->leagues = $leagues;
		$this->tournaments = $tournaments;

		add_action( 'admin_post_add_league', array( $this, 'add_league' ) );
		$this->slug = 'leagues';
	}

	public function add_admin_menu() {
		add_admin_menu_separator( 26 );
		add_menu_page(
			'Leagues',
			'Leagues',
			'publish_pages',
			'leagues',
			array( $this, 'load_league_menu' ),
			'dashicons-star-empty',
			30
		);
		add_admin_menu_separator( 31 );
	}

	public function load_league_menu() {
		if ( ! current_user_can( 'publish_pages' ) ) {
			wp_die( 'You do not have sufficient permissions to access this page.' );
		}

		switch ( $this->current_action() ) {
			case 'delete':
				check_admin_referer( 'delete_league', '_wpnonce' );
				if ( isset( $_GET['id'] ) && is_numeric( $_GET['id'] ) ) {
					$this->leagues->delete( $_GET['id'] );
				}
				wp_redirect( $this->get_url( array( 'deleted' => 'true' ) ) );
		}

		wp_enqueue_script( 'league-admin' );
		wp_enqueue_style( 'league-admin' );
		require_once LEAGUE_PLUGIN_DIR . 'includes/view/admin/class-leagues-list-table.php';
		$league_list = new Leagues_List_Table( $this );
		$league_list->prepare_items();
		include_once LEAGUE_PLUGIN_DIR . 'templates/league-admin.php';
	}

	public function ajax_callbacks() {
		add_action( 'wp_ajax_get_league', array( $this, 'ajax_get_league' ) );
	}

	public function ajax_get_league() {
		if ( isset( $_POST['league'] ) && is_numeric( $_POST['league'] ) ) {
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

		if ( isset( $_POST['league'] ) ) {
			$this->leagues->add_league(
				sanitize_text_field( $_POST['league']['name'] ),
				sanitize_text_field( $_POST['league']['start'] ),
				sanitize_text_field( $_POST['league']['end'] )
			);
		}

		wp_redirect( add_query_arg( 'updated', 'true', admin_url( 'admin.php?page=leagues' ) ) );
	}

	public function get_url( $query_arg ) {
		return add_query_arg( array_merge( array( 'page' => $this->slug ), $query_arg ), admin_url( 'admin.php' ) );
	}

	public function get_edit_url( $id ) {
		return $this->get_url( array( 'action' => 'edit', 'id' => $id ) );
	}

	public function get_delete_url( $id ) {
		return $this->get_url( array( 'action' => 'delete', 'id' => $id, '_wpnonce' => wp_create_nonce( 'delete_league' ) ) );
	}

	public function get_all_leagues() {
		return $this->leagues->get_all();
	}

	public function get_tournament_count( $id ) {
		return count( $this->tournaments->get_all_for_league( $id ) );
	}
}