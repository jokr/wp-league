<?php

require_once dirname( __FILE__ ) . '/class-repository.php';
require_once dirname( __DIR__ ) . '/model/class-league.php';

class Leagues extends Repository
{
	public function __construct() {
		parent::__construct();
		global $wpdb;

		$this->table = $wpdb->prefix . 'leagues';
		$wpdb->leagues = $this->table;
		$this->columns = 'id, name, start, end, standings';
		$this->sort = 'start';
	}

	public function get_by_id( $id ) {
		assert( isset( $id ) && is_numeric( $id ) );
		return League::from_array( $id, parent::get_by_id( $id ) );
	}

	public function get_all() {
		$result = array();
		foreach ( parent::get_all() as $league ) {
			array_push( $result, League::from_array( $league['id'], $league ) );
		}
		return $result;
	}

	public function get_all_active() {
		$result = array();
		foreach ( parent::query( "WHERE end >= CURDATE()" ) as $league ) {
			array_push( $result, League::from_array( $league['id'], $league ) );
		}
		return $result;
	}

	public function create_table() {
		global $wpdb;

		$sql = "CREATE TABLE $wpdb->leagues (
			id MEDIUMINT NOT NULL AUTO_INCREMENT,
			created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			name VARCHAR(32) NOT NULL,
			start DATE NOT NULL,
			end DATE NOT NULL,
			standings MEDIUMTEXT NOT NULL,
			PRIMARY KEY id (id)
		)
		DEFAULT COLLATE utf8_general_ci;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbdelta( $sql );
	}
}