<?php

require_once dirname( __FILE__ ) . '/class-repository.php';
require_once dirname( __DIR__ ) . '/model/class-player.php';

class Players extends Repository
{
	public function __construct() {
		parent::__construct();
		global $wpdb;

		$this->table = $wpdb->prefix . 'players';
		$wpdb->players = $this->table;
		$this->sort = 'first';
		$this->columns = 'id, first, last, dci, credits, wp_user_id';
	}

	public function get_by_id( $id ) {
		return new Player(parent::_get_by_id( $id ));
	}

	public function get_all() {
		return $this->get_objects( parent::_get_all() );
	}

	public function find_by_dci( $dci ) {
		$result = $this->get_objects( parent::_query( "WHERE dci = $dci" ) );
		if ( empty($result) ) {
			return null;
		} else {
			return $result[0];
		}
	}

	public function create_table() {
		global $wpdb;

		$sql = "CREATE TABLE $wpdb->players (
			id MEDIUMINT NOT NULL AUTO_INCREMENT,
			created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			first VARCHAR(32) NOT NULL,
			last VARCHAR(32) NOT NULL,
			dci VARCHAR(16) NOT NULL,
			credits SMALLINT NOT NULL DEFAULT '0',
			wp_user_id BIGINT DEFAULT NULL,
			PRIMARY KEY id (id),
			UNIQUE KEY dci (dci),
			INDEX user_ind (wp_user_id),
			FOREIGN KEY (wp_user_id)
				REFERENCES $wpdb->users(id)
		)
		DEFAULT COLLATE utf8_general_ci;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbdelta( $sql );
	}

	private function get_objects( array $players ) {
		$result = array();
		foreach ( $players as $id => $player ) {
			$result[$id] = new Player($player);
		}
		return $result;
	}
}