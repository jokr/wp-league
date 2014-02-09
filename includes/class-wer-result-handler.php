<?php

class WER_Result_Handler
{
	private $url;
	private $xml;
	private $tournament;

	private $standings;
	private $matches;
	private $saved;

	public function __construct( $url, SimpleXMLElement $xml, Tournament $tournament ) {
		$this->url = $url;
		$this->xml = $xml;
		$this->tournament = $tournament;

		$this->read_standings();
		$this->read_matches();

		$this->saved = false;
	}

	private function read_standings() {
		$this->standings = array();

		$participants = array();
		foreach ( $this->xml->xpath( '//participation//person' ) as $participant ) {
			$participants[(string) $participant->attributes()->id] = array(
				'first' => (string) $participant->attributes()->first,
				'last' => (string) $participant->attributes()->last
			);
		}

		$players = $this->xml->xpath( "//participation//ref" );
		foreach ( $players as $player ) {
			$participant = $participants[(string) $player->attributes()->person];
			$participant['dci'] = (string) $player->attributes()->person;
			$participant['rank'] = (int) $player->attributes()->seq;
			$participant['points'] = 0;
			$this->standings[(string) $player->attributes()->person] = $participant;
		}
	}

	private function read_matches() {
		$this->matches = array();

		foreach ( $this->xml->xpath( '//matches//round' ) as $round ) {
			$date = date( 'Y-m-d H:i:s', strtotime( (string) $round->attributes()->date ) );
			$round_nr = (int) $round->attributes()->number;
			foreach ( $round->xpath( 'match' ) as $matchXML ) {
				$match = array(
					'tournament_id' => $this->tournament->get_id(),
					'round' => $round_nr,
					'date' => $date,
					'player_dci' => (string) $matchXML->attributes()->person,
					'outcome' => (int) $matchXML->attributes()->outcome
				);
				// As long as match is not a bye or a match loss, record opponent and match details.
				if ( ! in_array( $match['outcome'], array(3, 5) ) ) {
					$match['opponent_dci'] = (string) $matchXML->attributes()->opponent;
					$match['wins'] = (int) $matchXML->attributes()->win;
					$match['losses'] = (int) $matchXML->attributes()->loss;
					$match['draws'] = (int) $matchXML->attributes()->draw;
				}

				array_push( $this->matches, $match );

				switch ( $match['outcome'] ) {
					case 2: // draw
						$this->standings[$match['player_dci']]['points'] += 1;
						$this->standings[$match['opponent_dci']]['points'] += 1;
						break;
					case 3: // bye
					case 1: // win
						$this->standings[$match['player_dci']]['points'] += 3;
				}
			}
		}
	}

	private function save_participants( Players $players ) {
		foreach ( $this->standings as &$standing ) {
			if ( ! $player = $players->find_by_dci( $standing['dci'] ) ) {
				$player = new Player($standing);
				$player->save();
				$standing['id'] = $player->get_id();
			}
			$standing['id'] = $player->get_id();
		}
	}

	private function save_matches( Matches $matches ) {
		foreach ( $this->matches as &$match ) {
			$player = $this->standings[$match['player_dci']];
			if ( isset($player) ) {
				$match['player_id'] = $player['id'];
				unset($match['player_dci']);
				if ( ! in_array($match['outcome'], array(3, 5) ) ) {
					$opponent = $this->standings[$match['opponent_dci']];
					if ( isset($opponent) ) {
						$match['opponent_id'] = $opponent['id'];
						unset($match['opponent_dci']);
					}
				}
			}

			if ( ! $matches->exists_in_tournament( $match['tournament_id'], $match['round'], $match['player_id'] ) ) {
				$m = new Match($match);
				$m->save();
				$match['id'] = $m->get_id();
			}
		}
	}

	public function save_results( Players $players, Matches $matches ) {
		if ( $this->saved ) {
			return new WP_Error('500', "These results already have been saved.");
		}
		$this->save_participants( $players );
		$this->save_matches( $matches );
		$this->tournament->add_results( $this->url, $this->get_abbreviated_standings() );
		$this->tournament->save();

		$this->saved = true;
		return array($this->standings, $this->matches);
	}

	private function get_abbreviated_standings() {
		$result = array();
		foreach ( $this->standings as $standing ) {
			$result[$standing['rank']] = array(
				"player" => $standing['id'],
				"points" => $standing['points']
			);
		}
		return $result;
	}
}