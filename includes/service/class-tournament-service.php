<?php

class Tournament_Service
{
	private $tournaments;
	private $players;
	private $matches;
	private $events;

	public function __construct( Leagues $leagues, Tournaments $tournaments, Players $players, Matches $matches, League_Events $events ) {
		$this->leagues = $leagues;
		$this->tournaments = $tournaments;
		$this->players = $players;
		$this->matches = $matches;
		$this->events = $events;
	}

	public function get_all() {
		return $this->tournaments->get_all();
	}

	public function get_by_id( $id ) {
		$tournament = $this->tournaments->get_by_id( $id );
		if ( $tournament->get_status() != 'OPEN' ) {
			$tournament->set_matches( $this->matches->get_all_by_tournament( $id ) );
		}
		return $tournament;
	}

	public function get_all_for_league( $id ) {
		return $this->tournaments->get_by_league( $id );
	}

	public function create( $league_id, $date, $format, $url ) {
		$tournament = new Tournament( $league_id, $date, $format );
		$tournament->set_url( $url );
		$this->tournaments->save( $tournament );
		return $tournament;
	}

	public function update( $id, $league_id, $date, $format, $url ) {
		$tournament = $this->tournaments->get_by_id( $id );
		if ( isset( $tournament ) ) {
			$tournament->set_league_id( $league_id );
			$tournament->set_date( $date );
			$tournament->set_format( $format );
			$tournament->set_url( $url );
			$this->tournaments->save( $tournament );
		} else {
			return false;
		}
		return true;
	}

	public function exists( $id ) {
		return $this->tournaments->exists( $id );
	}

	public function save_results( $id, $standings, $matches, $xml ) {
		foreach ( $standings as &$standing ) {
			if ( ! $player = $this->players->find_by_dci( $standing['dci'] ) ) {
				$player = new Player( $standing['first'], $standing['last'], $standing['dci'] );
				$this->players->save( $player );
			}
			$standing['id'] = $player->get_id();
		}

		foreach ( $matches as &$match ) {
			$player = $standings[$match['player_dci']];
			if ( isset( $player ) ) {
				$match['player_id'] = $player['id'];
				unset( $match['player_dci'] );
				if ( ! in_array( $match['outcome'], array( 3, 5 ) ) ) {
					$opponent = $standings[$match['opponent_dci']];
					if ( isset( $opponent ) ) {
						$match['opponent_id'] = $opponent['id'];
						unset( $match['opponent_dci'] );
					}
				}
			}

			if ( ! $this->matches->exists_in_tournament( $id, $match['round'], $match['player_id'] ) ) {
				$m = new Match( $id, $match['round'], $match['date'], $match['player_id'], $match['outcome'] );
				if ( isset( $match['opponent_id'] ) ) {
					$m->set_opponent_id( $match['opponent_id'] );
				}
				if ( isset( $match['wins'] ) ) {
					$m->set_wins( $match['wins'] );
				}
				if ( isset( $match['losses'] ) ) {
					$m->set_losses( $match['losses'] );
				}
				if ( isset( $match['draws'] ) ) {
					$m->set_draws( $match['draws'] );
				}
				$match['id'] = $this->matches->save( $m );
			}
		}

		$abbr_results = array();
		foreach ( $standings as $std ) {
			$abbr_results[$std['rank']] = array(
				"player" => $std['id'],
				"points" => $std['points']
			);
		}
		$tournament = $this->tournaments->get_by_id( $id );
		$tournament->add_results( $xml, $abbr_results );
		$this->tournaments->save( $tournament );
	}

	public function save_points( $id, $standings ) {
		if ( $tournament = $this->tournaments->get_by_id( $id ) ) {
			$league = $this->leagues->get_by_id( $tournament->get_league_id() );
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
			$tournament->set_status( 'CLOSED' );
			$this->leagues->save( $league );
			$this->tournaments->save( $tournament );
		}
	}

	public function delete_results( $id ) {
		$tournament = $this->tournaments->get_by_id( $id );
		if ( $currentFile = $tournament->get_xml() ) {
			unlink( $currentFile );
		}
		$tournament->delete_results();
		$this->tournaments->save( $tournament );
		$this->matches->delete_all_by_tournament( $id );
	}

	public function update_tournament_status() {
		$this->tournaments->update_tournament_status();
	}
}