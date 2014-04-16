<?php

require_once dirname( __FILE__ ) . '/class-league-event.php';

class Credit_Points extends League_Event
{
	private $credits;

	public function __construct( Player $player, $credits, $date ) {
		parent::__construct( $player );
		$this->credits = $credits;
		$this->date = $date;
	}

	protected function _apply() {
		$this->player->award_credits( $this->credits );
	}

	function get_message() {
		return sprintf( '%u credits awarded.', $this->credits );
	}

	function get_date() {
		return $this->date;
	}

	public function get_type() {
		return 'CREDIT_POINTS';
	}

	public function get_credits() {
		return $this->credits;
	}

	public function get_params() {
		return array(
			'credits' => $this->credits
		);
	}
}