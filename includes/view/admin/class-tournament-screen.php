<?php

require_once dirname( __FILE__ ) . '/class-admin-screen.php';

class Tournament_Screen extends Admin_Screen
{
	private $tournaments;
	private $leagues;
	private $matches;
	private $players;

	public function __construct( Tournaments $tournaments, Leagues $leagues, Players $players, Matches $matches ) {
		parent::__construct();
		$this->tournaments = $tournaments;
		$this->leagues = $leagues;
		$this->players = $players;
		$this->matches = $matches;

		add_action( 'admin_post_add_tournament', array( $this, 'add_tournament' ) );
		add_action( 'admin_post_edit_tournament', array( $this, 'edit_tournament' ) );
		add_action( 'admin_post_upload_results', array( $this, 'upload_results' ) );
		add_action( 'admin_post_delete_results', array( $this, 'delete_results' ) );
		add_action( 'admin_post_save_points', array( $this, 'save_points' ) );
		add_filter( 'upload_mimes', array( $this, 'add_custom_upload_mimes' ) );
	}

	public function add_admin_menu() {
		add_submenu_page(
			'leagues',
			'Tournaments',
			'Tournaments',
			'publish_pages',
			'tournaments',
			array( $this, 'load_tournament_menu' )
		);
	}

	public function load_tournament_menu() {
		switch ( $this->current_action() ) {
			case 'edit':
				load_template( LEAGUE_PLUGIN_DIR . '/templates/tournament-edit.php' );
				break;
			default:
				load_template( LEAGUE_PLUGIN_DIR . '/templates/tournament-admin.php' );
		}
	}

	public function add_tournament() {
		if ( ! current_user_can( 'publish_pages' ) ) {
			wp_die( 'You do not have sufficient permissions to access this page.' );
		}

		check_admin_referer( 'add-tournament', '_wpnonce_add_tournament' );
		if ( isset( $_POST['tournament'] ) ) {
			$tournament = $_POST['tournament'];
			if (
				! isset( $tournament['league_id'] ) ||
				! is_numeric( $tournament['league_id'] ) ||
				! $this->leagues->exists( $tournament['league_id'] )
			) {
				wp_die( 'Invalid league id' );
			}
			$tournament = new Tournament( array(
				'date' => sanitize_text_field( $tournament['date'] ),
				'format' => sanitize_text_field( $tournament['format'] ),
				'url' => sanitize_text_field( $tournament['url'] ),
				'league_id' => $tournament['league_id']
			) );

			$this->tournaments->save( $tournament );
		}

		wp_redirect( add_query_arg( 'updated', 'true', admin_url( 'admin.php?page=tournaments' ) ) );
	}

	public function edit_tournament() {
		if ( ! current_user_can( 'publish_pages' ) ) {
			wp_die( 'You do not have sufficient permissions to access this page.' );
		}

		check_admin_referer( 'edit-tournament', '_wpnonce_edit_tournament' );
		if ( isset( $_POST['id'] ) && isset( $_POST['tournament'] ) ) {
			$updated = $_POST['tournament'];
			if ( isset( $updated['league_id'] ) &&
				is_numeric( $updated['league_id'] ) &&
				$this->leagues->exists( $updated['league_id'] )
			) {
				$tournament = $this->tournaments->get_by_id( $_POST['id'] );
				$tournament->setLeagueId( $updated['league_id'] );
				$tournament->setDate( sanitize_text_field( $updated['date'] ) );
				$tournament->setFormat( sanitize_text_field( $updated['format'] ) );
				$tournament->setUrl( sanitize_text_field( $updated['url'] ) );
				$this->tournaments->save( $tournament );
				wp_redirect( add_query_arg( 'updated', 'true', admin_url( 'admin.php?page=tournaments' ) ) );
			} else {
				wp_die( 'No valid league.' );
			}
		} else {
			wp_die( 'No valid tournament.' );
		}

	}

	public function upload_results() {
		check_admin_referer( 'upload-results', '_wpnonce_upload_results' );
		if ( isset( $_FILES['results-file'] ) &&
			isset( $_POST['id'] ) &&
			is_numeric( $_POST['id'] )
		) {
			$tournament = $this->tournaments->get_by_id( $_POST['id'] );
			if ( $currentFile = $tournament->getXml() ) {
				unlink( $currentFile );
			}
			$resultFile = $_FILES['results-file'];
			$file = wp_handle_upload( $resultFile, array( 'test_form' => false ) );
			$xml = simplexml_load_file( $file['file'] );
			$importer = new WER_Result_Handler( $file['file'], $xml, $tournament );
			$importer->save_results( $this->players, $this->matches );

			wp_redirect( admin_url( 'admin.php?' . http_build_query( array(
					'page' => 'tournaments',
					'updated' => 'true',
					'action' => 'edit',
					'id' => $tournament->get_id()
				) )
			) );
		} else {
			wp_redirect( add_query_arg( 'updated', 'false', admin_url( 'admin.php?page=tournaments' ) ) );
		}
	}

	public function delete_results() {
		check_admin_referer( 'delete-results', '_wpnonce_delete_results' );
		if ( isset( $_POST['id'] ) && is_numeric( $_POST['id'] ) && $this->tournaments->exists( $_POST['id'] ) ) {
			$tournament = $this->tournaments->get_by_id( $_POST['id'] );
			if ( $currentFile = $tournament->getXml() ) {
				unlink( $currentFile );
			}
			$tournament->delete_results();
			$tournament->save();
			$this->matches->delete_all_by_tournament( $tournament->get_id() );
			wp_redirect( admin_url( 'admin.php?' . http_build_query( array(
					'page' => 'tournaments',
					'updated' => 'true',
					'action' => 'edit',
					'id' => $tournament->get_id()
				) )
			) );
			wp_redirect( admin_url( 'admin.php?' . http_build_query( array(
					'page' => 'tournaments',
					'deleted' => 'true',
					'action' => 'edit',
					'id' => $tournament->get_id()
				) )
			) );
		} else {
			wp_redirect( add_query_arg( 'updated', 'false', admin_url( 'admin.php?page=tournaments' ) ) );
		}
	}

	public function save_points() {
		check_admin_referer( 'save-points', '_wpnonce_save_points' );
		if (
			isset( $_POST['id'] ) &&
			is_numeric( $_POST['id'] ) &&
			$this->tournaments->exists( $_POST['id'] ) &&
			isset( $_POST['players'] ) &&
			is_array( $_POST['players'] )
		) {
			require_once LEAGUE_PLUGIN_DIR . 'includes/domain/model/events/class-participated-tournament.php';

			$tournament = $this->tournaments->get_by_id( $_POST['id'] );
			foreach ( $_POST['players'] as $id => $player ) {
				$event = new Participated_Tournament(
					$this->players->get_by_id( $id ),
					$tournament,
					$player['rank'],
					isset( $player['winner'] ),
					$player['league'],
					$player['credits']
				);
				$event->apply();
			}

			$tournament->setStatus( 'CLOSED' );
			$tournament->save();

			wp_redirect( admin_url( 'admin.php?' . http_build_query( array(
					'page' => 'tournaments',
					'updated' => 'true',
					'action' => 'edit',
					'id' => $tournament->get_id()
				) )
			) );
		} else {
			wp_redirect( add_query_arg( 'updated', 'false', admin_url( 'admin.php?page=tournaments' ) ) );
		}
	}

	public function add_custom_upload_mimes( $existing_mimes ) {
		$existing_mimes['xml'] = 'application/atom+xml';
		return $existing_mimes;
	}
}