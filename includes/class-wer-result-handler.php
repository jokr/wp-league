<?php

class WER_Result_Handler
{
	private $url;
	private $xml;

	private $standings;
	private $matches;

	public function __construct( $url, SimpleXMLElement $xml ) {
		$this->url = $url;
		$this->xml = $xml;

		$this->read_standings();
		$this->read_matches();

	}

	private function read_standings() {
		$this->standings = array();

		$participants = array();
		foreach ( $this->xml->xpath( '//participation//person' ) as $participant ) {
			$participants[(string)$participant->attributes()->id] = array(
				'first' => (string)$participant->attributes()->first,
				'last' => (string)$participant->attributes()->last
			);
		}

		$players = $this->xml->xpath( "//participation//ref" );
		foreach ( $players as $player ) {
			$participant = $participants[(string)$player->attributes()->person];
			$participant['dci'] = (string)$player->attributes()->person;
			$participant['rank'] = (int)$player->attributes()->seq;
			$participant['points'] = 0;
			$this->standings[(string)$player->attributes()->person] = $participant;
		}
	}

	private function read_matches() {
		$this->matches = array();

		foreach ( $this->xml->xpath( '//matches//round' ) as $round ) {
			$date = date( 'Y-m-d H:i:s', strtotime( (string)$round->attributes()->date ) );
			$round_nr = (int)$round->attributes()->number;
			foreach ( $round->xpath( 'match' ) as $matchXML ) {
				$match = array(
					'round' => $round_nr,
					'date' => $date,
					'player_dci' => (string)$matchXML->attributes()->person,
					'outcome' => (int)$matchXML->attributes()->outcome
				);
				// As long as match is not a bye or a match loss, record opponent and match details.
				if ( ! in_array( $match['outcome'], array( 3, 5 ) ) ) {
					$match['opponent_dci'] = (string)$matchXML->attributes()->opponent;
					$match['wins'] = (int)$matchXML->attributes()->win;
					$match['losses'] = (int)$matchXML->attributes()->loss;
					$match['draws'] = (int)$matchXML->attributes()->draw;
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

	public function get_standings() {
		return $this->standings;
	}

	public function get_matches() {
		return $this->matches;
	}
}