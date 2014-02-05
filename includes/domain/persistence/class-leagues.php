<?php

require_once dirname( __FILE__ ) . '/class-repository.php';
require_once dirname( __DIR__ ) . '/model/class-league.php';

class Leagues extends Repository
{
	private $tournaments;

	public function __construct( Tournaments $tournaments ) {
		parent::__construct();
		$this->tournaments = $tournaments;

		global $wpdb;

		$this->table = $wpdb->prefix . 'leagues';
		$wpdb->leagues = $this->table;
		$this->columns = 'id, name, start, end';
		$this->sort = 'start';
	}

	public function get_by_id( $id ) {
		$league = parent::_get_by_id( $id );
		$league['tournaments'] = $this->tournaments->get_by_league( $league['id'] );
		return new League($league);
	}

	public function get_all() {
		return $this->get_objects( parent::_get_all() );
	}

	public function get_all_active() {
		return $this->get_objects( parent::_query( "WHERE end >= CURDATE()" ) );
	}

	public function create_table() {
		global $wpdb;

		$sql = "CREATE TABLE $wpdb->leagues (
			id MEDIUMINT NOT NULL AUTO_INCREMENT,
			created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			name VARCHAR(32) NOT NULL,
			start DATE NOT NULL,
			end DATE NOT NULL,
			options VARCHAR(255) NOT NULL,
			PRIMARY KEY id (id)
		)
		DEFAULT COLLATE utf8_general_ci;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbdelta( $sql );
	}

	private function get_objects( array $leagues ) {
		$result = array();
		foreach ( $leagues as $id => $league ) {
			$league['tournaments'] = $this->tournaments->get_by_league( $league['id'] );
			$result[$id] = new League($league);
		}
		return $result;
	}
}