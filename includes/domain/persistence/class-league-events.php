<?php

include_once LEAGUE_PLUGIN_DIR . 'includes/domain/model/events/class-credit-points.php';
include_once LEAGUE_PLUGIN_DIR . 'includes/domain/model/events/class-tournament-credit-points.php';
include_once LEAGUE_PLUGIN_DIR . 'includes/domain/model/events/class-participated-tournament.php';
include_once LEAGUE_PLUGIN_DIR . 'includes/domain/model/events/class-league-points.php';

class League_Events extends Repository
{
	public function __construct() {
		parent::__construct();

		global $wpdb;

		$this->table = $wpdb->prefix . 'league_events';
		$wpdb->league_events = $this->table;
		$this->columns = 'id, date, type, player_id, league_id, tournament_id, params';
		$this->sort = 'date desc';
	}

	public function create_table() {
		global $wpdb;

		$sql = "CREATE TABLE $wpdb->league_events (
			id MEDIUMINT NOT NULL AUTO_INCREMENT,
			date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			type TINYTEXT NOT NULL,
			player_id MEDIUMINT NOT NULL,
			league_id MEDIUMINT,
			tournament_id MEDIUMINT,
			params MEDIUMTEXT NOT NULL,
			PRIMARY KEY id (id)
			INDEX player_ind (player_id),
			FOREIGN KEY (player_id)
				REFERENCES $wpdb->players(id)
		)
		DEFAULT COLLATE utf8_general_ci;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbdelta( $sql );
	}

	public function get_by_player( Player $player ) {
		$p_id = $player->get_id();
		foreach ( $result = parent::query( "WHERE player_id = $p_id ORDER BY date desc" ) as &$event ) {
			$event['params'] = unserialize( $event['params'] );
		}
		return $result;
	}
}