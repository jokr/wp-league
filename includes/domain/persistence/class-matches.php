<?php

require_once dirname( __FILE__ ) . '/class-repository.php';
require_once dirname( __DIR__ ) . '/model/class-match.php';

class Matches extends Repository
{
	public function __construct() {
		parent::__construct();
		global $wpdb;

		$this->table = $wpdb->prefix . 'matches';
		$wpdb->matches = $this->table;
		$this->sort = 'date';
		$this->columns = 'id, tournament_id, round, date, player_id, opponent_id, outcome, wins, losses, draws';
	}

	public function get_by_id( $id ) {
		$result = parent::get_by_id( $id );
		if ( isset( $result ) ) {
			return Match::from_array( $id, $result );
		} else {
			return null;
		}
	}

	public function get_all() {
		$result = array();
		foreach ( parent::get_all() as $match ) {
			array_push( $result, Match::from_array( $match['id'], $match ) );
		}
		return $result;
	}

	public function get_all_by_tournament( $id ) {
		$result = array();
		foreach ( parent::query( "WHERE tournament_id = $id" ) as $match ) {
			array_push( $result, Match::from_array( $match['id'], $match ) );
		}
		return $result;
	}

	public function create_table() {
		global $wpdb;

		$sql = "CREATE TABLE $wpdb->matches (
			id MEDIUMINT NOT NULL AUTO_INCREMENT,
			created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			tournament_id MEDIUMINT NOT NULL,
			round SMALLINT NOT NULL,
			date DATETIME NOT NULL,
			player_id MEDIUMINT NOT NULL,
			opponent_id MEDIUMINT,
			outcome SMALLINT NOT NULL,
			wins SMALLINT,
			losses SMALLINT,
			draws SMALLINT,
			PRIMARY KEY id (id),
			INDEX tournament_ind (tournament_id),
			INDEX player_ind (player_id),
			INDEX opponent_ind (opponent_id),
			FOREIGN KEY (tournament_id)
				REFERENCES $wpdb->tournaments(id)
				ON DELETE CASCADE,
			FOREIGN KEY (player_id)
				REFERENCES $wpdb->players(id),
			FOREIGN KEY (opponent_id)
				REFERENCES $wpdb->players(id)
		)
		DEFAULT COLLATE utf8_general_ci;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbdelta( $sql );
	}

	public function exists_in_tournament( $tournament_id, $round, $player_id ) {
		global $wpdb;
		$result = $wpdb->get_row( "SELECT id FROM $this->table WHERE tournament_id = $tournament_id
		AND round = $round AND (player_id = $player_id OR opponent_id = $player_id)" );
		return ! empty( $result );
	}

	public function delete_all_by_tournament( $tournamentId ) {
		global $wpdb;
		return $wpdb->delete( $this->table, array( 'tournament_id' => $tournamentId ) );
	}
}
 