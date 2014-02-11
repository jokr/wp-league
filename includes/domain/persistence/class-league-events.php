<?php

include_once LEAGUE_PLUGIN_DIR . 'includes/domain/model/events/class-credit-points.php';
include_once LEAGUE_PLUGIN_DIR . 'includes/domain/model/events/class-tournament-credit-points.php';

class League_Events extends Repository
{
    public function __construct() {
        parent::__construct();

        global $wpdb;

        $this->table = $wpdb->prefix . 'league_events';
        $wpdb->league_events = $this->table;
        $this->columns = 'id, date, type, player_id, league_id, message, params';
        $this->sort = 'date';
    }

    public function get_by_id( $id ) {
        // TODO: Implement get_by_id() method.
    }

    public function get_all() {
        // TODO: Implement get_all() method.
    }

    public function get_credits( Tournament $tournament, Player $player ) {
        $t_id = $tournament->get_id();
        $p_id = $player->get_id();
        $result = parent::_query( "WHERE type = 'CREDIT_POINTS' AND tournament_id = $t_id AND player_id = $p_id" );
        if ( !empty( $result ) ) {
            $params = unserialize( $result[0]['params'] );
            return $params['credits'];
        } else {
            return 0;
        }
    }

    public function get_league_points( Tournament $tournament, Player $player ) {
        $t_id = $tournament->get_id();
        $p_id = $player->get_id();
        $result = parent::_query( "WHERE type = 'LEAGUE POINTS' AND tournament_id = $t_id AND player_id = $p_id" );
        if ( !empty( $result ) ) {
            $params = unserialize( $result[0]['params'] );
            return $params['points'];
        } else {
            return 0;
        }
    }

    public function is_winner( Tournament $tournament, Player $player ) {
        $t_id = $tournament->get_id();
        $p_id = $player->get_id();
        $result = parent::_query( "WHERE type = 'LEAGUE POINTS' AND tournament_id = $t_id AND player_id = $p_id" );
        if ( !empty( $result ) ) {
            $params = unserialize( $result[0]['params'] );
            return $params['winner'];
        } else {
            return false;
        }
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
			message MEDIUMTEXT,
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
        return parent::_query( "WHERE player_id = $p_id" );
    }
}