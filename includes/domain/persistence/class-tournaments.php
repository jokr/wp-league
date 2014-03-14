<?php

require_once dirname( __FILE__ ) . '/class-repository.php';
require_once dirname( __DIR__ ) . '/model/class-tournament.php';

class Tournaments extends Repository
{
	public function __construct() {
		parent::__construct();

		global $wpdb;

		$this->table = $wpdb->prefix . 'tournaments';
		$wpdb->leagues = $this->table;
		$this->sort = 'date';
		$this->columns = 'id, league_id, date, format, status, url, standings, xml';
	}

	public function get_all() {
		$result = array();
		foreach ( parent::get_all() as $tournament ) {
			array_push( $result, Tournament::from_array( $tournament['id'], $tournament ) );
		}
		return $result;
	}

	public function get_by_id( $id ) {
		$result = parent::get_by_id( $id );
		if ( isset( $result ) ) {
			return Tournament::from_array( $id, $result );
		} else {
			return null;
		}
	}

	public function get_by_league( $id ) {
		$result = array();
		foreach ( parent::query( "WHERE league_id = $id" ) as $tournament ) {
			array_push( $result, Tournament::from_array( $tournament['id'], $tournament ) );
		}
		return $result;
	}

	public function create_table() {
		global $wpdb;

		$sql = "CREATE TABLE $wpdb->tournaments (
			id MEDIUMINT NOT NULL AUTO_INCREMENT,
			created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			league_id MEDIUMINT NOT NULL,
			date DATETIME NOT NULL,
			format VARCHAR(32) NOT NULL DEFAULT 'OPEN',
			status VARCHAR(32) NOT NULL,
			url VARCHAR(255),
			standings MEDIUMTEXT,
			xml VARCHAR(255),
			PRIMARY KEY id (id),
			INDEX league_ind (league_id),
			FOREIGN KEY (league_id)
				REFERENCES $wpdb->leagues(id)
				ON DELETE CASCADE
		)
		DEFAULT COLLATE utf8_general_ci;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbdelta( $sql );
	}
}