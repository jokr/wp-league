<?php

require_once dirname( __FILE__ ) . '/class-league-event.php';
require_once dirname( __FILE__ ) . '/class-credit-points.php';
require_once dirname( __FILE__ ) . '/class-league-points.php';

class Participated_Tournament extends League_Event
{
	private $league;
	private $tournament;
	private $rank;
	private $winner;
	private $credit_event;
	private $points_event;

	public function __construct( Player $player, League $league, Tournament $tournament, $rank, $winner, $points, $credits ) {
		parent::__construct( $player );
		$this->league = $league;
		$this->tournament = $tournament;
		$this->rank = $rank;
		if ( $credits > 0 ) {
			$this->credit_event = new Tournament_Credit_Points( $player, $tournament, $credits );
		}
		if ( $points > 0 ) {
			$this->points_event = new League_Points( $player, $this->league, $tournament, $points, $winner, $this->get_date() );
		}
	}

	protected function _apply() {
		$this->league->add_player( $this->player->get_id() );

		if ( isset( $this->credit_event ) ) {
			$this->credit_event->apply();
		}
		if ( isset( $this->points_event ) ) {
			$this->points_event->apply();
		}

		if ( $this->winner ) {
			$this->tournament->set_winner( $this->player->get_id() );
		}
	}

	public function get_message() {
		return __( sprintf( 'You played in a tournament on %s and finished %u.',
			date_i18n( get_option( 'date_format' ), strtotime( $this->get_date() ) ),
			$this->rank
		), 'league' );
	}

	public function get_date() {
		return $this->tournament->get_date();
	}

	public function get_type() {
		return 'PARTICIPATION_EVENT';
	}

	public function get_vars() {
		$result = parent::get_vars();
		$result['tournament_id'] = $this->tournament->get_id();
		$result['league_id'] = $this->league->get_id();
		return $result;
	}

	public function get_params() {
		$result = array( 'rank' => $this->rank );
		if ( isset( $this->credits ) ) {
			$result['credits'] = $this->credits->get_id();
		}
		if ( isset( $this->points ) ) {
			$result['points'] = $this->points->get_id();
		}
		return $result;
	}

	public function get_points_event() {
		return $this->points_event;
	}

	public function get_credit_event() {
		return $this->credit_event;
	}


}