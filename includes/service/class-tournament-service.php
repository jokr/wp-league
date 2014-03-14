<?php

class Tournament_Service
{
	private $tournaments;
	private $players;
	private $matches;

	public function __construct( Tournaments $tournaments, Players $players, Matches $matches ) {
		$this->tournaments = $tournaments;
		$this->players = $players;
		$this->matches = $matches;
	}

	public function get_all() {
		return $this->tournaments->get_all();
	}

	public function get_by_id( $id ) {
		return $this->tournaments->get_by_id( $id );
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
				$match['id'] = $this->matches->save( new Match(
					$id,
					$match['round'],
					$match['date'],
					$match['player_id'],
					isset($match['opponent_id']) ? $match['opponent_id'] : null,
					$match['outcome'],
					isset($match['wins']) ? $match['wins'] : null,
					isset($match['losses']) ? $match['losses'] : null,
					isset($match['draws']) ? $match['draws'] : null
				) );
			}
		}

		$abbr_results = array();
		foreach ( $standings as $standing ) {
			$abbr_results[$standing['rank']] = array(
				"player" => $standing['id'],
				"points" => $standing['points']
			);
		}

		$tournament = $this->tournaments->get_by_id($id);
		$tournament->add_results($xml, $abbr_results);
		$this->tournaments->save($tournament);
	}
}