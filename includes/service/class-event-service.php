<?php

class Event_Service
{
	private $events;
	private $players;
	private $leagues;
	private $tournaments;

	function __construct( League_Events $events, Players $players, Leagues $leagues, Tournaments $tournaments ) {
		$this->events = $events;
		$this->players = $players;
		$this->leagues = $leagues;
		$this->tournaments = $tournaments;
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
		$result = array();
		foreach ( $this->events->get_by_player( $player ) as $event ) {
			array_push( $result, $this->get_event_from_array( $event ) );
		}
		return $result;
	}

	private function get_event_from_array( array $event ) {
		switch ( $event['type'] ) {
			case 'PARTICIPATION_EVENT':
				$player = $this->players->get_by_id( $event['player_id'] );
				$league = $this->leagues->get_by_id( $event['league_id'] );
				$tournament = $this->tournaments->get_by_id( $event['tournament_id'] );
				return new Participated_Tournament(
					$player,
					$league,
					$tournament,
					$event['params']['rank'],
					$event['params']['winner']
				);
			case 'LEAGUE_POINTS':
				$player = $this->players->get_by_id( $event['player_id'] );
				$league = $this->leagues->get_by_id( $event['league_id'] );
				$tournament = $this->tournaments->get_by_id( $event['tournament_id'] );
				return new League_Points(
					$player,
					$league,
					$tournament,
					$event['params']['points'],
					$event['params']['winner'],
					$event['date']
				);
			case 'TOURNAMENT_CREDIT_POINTS':
				$player = $this->players->get_by_id( $event['player_id'] );
				$tournament = $this->tournaments->get_by_id( $event['tournament_id'] );
				return new Tournament_Credit_Points( $player, $tournament, $event['params']['credits'] );
			default:
				return null;
		}
	}
}