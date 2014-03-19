<?php

require_once dirname( __FILE__ ) . '/class-league-event.php';

class League_Points extends League_Event
{
	private $league;
	private $tournament;
	private $points;
	private $winner;
	private $date;

	public function __construct( Player $player, League $league, Tournament $tournament, $points, $winner, $date ) {
		parent::__construct( $player );
		$this->league = $league;
		$this->tournament = $tournament;
		$this->points = $points;
		$this->winner = $winner;
		$this->date = $date;
	}

	protected function _apply() {
		if ( $this->points > 0 ) {
			$this->league->add_league_points( $this->get_player()->get_id(), $this->points, $this->winner );
		}
	}

	public function get_message() {
		if ( $this->points > 1 ) {
			return __( sprintf( '%u league points awarded.', $this->points ), 'league' );
		} else {
			return __( sprintf( '%u league point awarded.', $this->points ), 'league' );
		}
	}

	public function get_date() {
		return $this->date;
	}

	public function get_type() {
		return 'LEAGUE POINTS';
	}

	public function get_vars() {
		$result = parent::get_vars();
		$result['league_id'] = $this->league->get_id();
		$result['tournament_id'] = $this->tournament->get_id();
		return $result;
	}

	public function get_params() {
		return array(
			'winner' => $this->winner,
			'points' => $this->points
		);
	}
}