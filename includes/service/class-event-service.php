<?php

class Event_Service
{
	private $events;

	function __construct( League_Events $events ) {
		$this->events = $events;
	}

	public function get_credit_points( $tournament_id, $player_id ) {
		$result = $this->events->query( "WHERE type = 'CREDIT_POINTS' AND tournament_id = $tournament_id AND player_id = $player_id" );
		if ( ! empty( $result ) ) {
			$params = unserialize( $result[0]['params'] );
			return $params['credits'];
		} else {
			return 0;
		}
	}

	public function get_league_points( $tournament_id, $player_id ) {
		$result = $this->events->query( "WHERE type = 'LEAGUE POINTS' AND tournament_id = $tournament_id AND player_id = $player_id" );
		if ( ! empty( $result ) ) {
			$params = unserialize( $result[0]['params'] );
			return $params['points'];
		} else {
			return 0;
		}
	}

	public function is_winner( $tournament_id, $player_id ) {
		$result = $this->events->query( "WHERE type = 'LEAGUE POINTS' AND tournament_id = $tournament_id AND player_id = $player_id" );
		if ( ! empty( $result ) ) {
			$params = unserialize( $result[0]['params'] );
			return $params['winner'];
		} else {
			return false;
		}
	}

	public function get_by_player( $player ) {
		return $this->events->get_by_player( $player );
	}
}