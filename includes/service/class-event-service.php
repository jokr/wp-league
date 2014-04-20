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
		$result = $this->events->query( "WHERE type = 'TOURNAMENT_CREDIT_POINTS' AND tournament_id = $tournament_id AND player_id = $player_id" );
		if ( ! empty( $result ) ) {
			$params = unserialize( $result[0]['params'] );
			return $params['credits'];
		} else {
			return 0;
		}
	}

	public function get_league_points( $tournament_id, $player_id ) {
		$result = $this->events->query( "WHERE type = 'LEAGUE_POINTS' AND tournament_id = $tournament_id AND player_id = $player_id" );
		if ( ! empty( $result ) ) {
			$params = unserialize( $result[0]['params'] );
			return $params['points'];
		} else {
			return 0;
		}
	}

	public function is_winner( $tournament_id, $player_id ) {
		$result = $this->events->query( "WHERE type = 'LEAGUE_POINTS' AND tournament_id = $tournament_id AND player_id = $player_id" );
		if ( ! empty( $result ) ) {
			$params = unserialize( $result[0]['params'] );
			return $params['winner'];
		} else {
			return false;
		}
	}

	public function get_by_player( $player_id ) {
		$result = array();
		foreach ( $this->events->get_by_player( $player_id ) as $event ) {
			$player = $this->players->get_by_id( $event['player_id'] );
			$league = $this->leagues->get_by_id( $event['league_id'] );
			$tournament = $this->tournaments->get_by_id( $event['tournament_id'] );
			array_push( $result, $this->get_event_from_array(
					$event,
					$player,
					$tournament,
					$league )
			);
		}
		return $result;
	}

	private function get_event_from_array( array $event, $player, $tournament, $league ) {
		$result = null;
		switch ( $event['type'] ) {
			case 'PARTICIPATION_EVENT':
				$result = new Participated_Tournament(
					$player,
					$league,
					$tournament,
					$event['params']['rank'],
					$event['params']['winner']
				);
				break;
			case 'LEAGUE_POINTS':
				$result = new League_Points(
					$player,
					$league,
					$tournament,
					$event['params']['points'],
					$event['params']['winner'],
					$event['date']
				);
				break;
			case 'TOURNAMENT_CREDIT_POINTS':
				$result = new Tournament_Credit_Points( $player, $tournament, $event['params']['credits'] );
		}
		$result->set_id( $event['id'] );
		return $result;
	}

	public function create_standings_events( Tournament $tournament, League $league, array $standings ) {
		foreach ( $standings as $id => $standing ) {
			$player = $this->players->get_by_id( $id );

			$event = new Participated_Tournament(
				$player,
				$league,
				$tournament,
				$standing['rank'],
				isset( $standing['winner'] ),
				$standing['league'],
				$standing['credits']
			);
			$event->apply();
			$this->events->save( $event );

			if ( $standing['league'] > 0 ) {
				$event = new League_Points(
					$player,
					$league,
					$tournament,
					$standing['league'],
					isset( $standing['winner'] ),
					$tournament->get_date()
				);

				$event->apply();
				$this->events->save( $event );
			}

			if ( $standing['credits'] > 0 ) {
				$event = new Tournament_Credit_Points( $player, $tournament, $standing['credits'] );
				$event->apply();
				$this->events->save( $event );
			}

			$this->players->save( $player );
		}
	}

	public function delete_results( Tournament $tournament ) {
		$league = $this->leagues->get_by_id( $tournament->get_league_id() );
		foreach ( $this->events->get_by_tournament( $tournament->get_id() ) as $event ) {
			$player = $this->players->get_by_id( $event['player_id'] );
			$event = $this->get_event_from_array( $event, $player, $tournament, $league );
			$event->rewind();
			$this->events->delete( $event->get_id() );
			$this->players->save( $player );
		}
		$this->leagues->save( $league );
	}
}