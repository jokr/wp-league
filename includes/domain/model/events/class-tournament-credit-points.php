<?php

require_once dirname( __FILE__ ) . '/class-credit-points.php';

class Tournament_Credit_Points extends Credit_Points
{
	private $tournament;

	public function __construct( Player $player, Tournament $tournament, $credits ) {
		parent::__construct( $player, $credits, $tournament->get_date() );
		$this->tournament = $tournament;
	}

	public function get_vars() {
		$result = parent::get_vars();
		$result['league_id'] = $this->tournament->get_league_id();
		$result['tournament_id'] = $this->tournament->get_id();
		return $result;
	}

	public function get_type() {
		return 'TOURNAMENT_CREDIT_POINTS';
	}
}