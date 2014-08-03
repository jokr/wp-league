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

	public function __construct( Player $player, League $league, Tournament $tournament, $rank, $winner ) {
		parent::__construct( $player );
		$this->league = $league;
		$this->tournament = $tournament;
		$this->rank = $rank;
	}

	public function apply() {
		assert( ! isset( $this->id ) );

		$this->league->add_player( $this->player->get_id() );

		if ( $this->winner ) {
			$this->tournament->set_winner( $this->player->get_id() );
		}
	}

	public function get_message() {
		return __( sprintf( 'You played in a tournament on %s and finished %s.',
			date_i18n( get_option( 'date_format' ), strtotime( $this->get_date() ) ),
			$this->get_rank_string( $this->rank )
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
		return $result;
	}

	public function rewind() {
		assert( isset( $this->id ) );
		$this->league->remove_player( $this->player->get_id() );
	}

	private function get_rank_string( $rank ) {
		switch ( $rank ) {
			case 1:
				return '1st';
			case 2:
				return '2nd';
			case 3:
				return '3rd';
			default:
				return $rank . 'th';
		}
	}
}