<?php

require_once dirname( __FILE__ ) . '/class-league-event.php';

class Credit_Points extends League_Event
{
	private $credits;
	private $reason;

	public function __construct( Player $player, $credits, $reason, $date ) {
		parent::__construct( $player );
		$this->credits = $credits;
		$this->reason = $reason;
		$this->date = $date;
	}

	protected function _apply() {
		$this->player->award_credits( $this->credits );
		$this->player->save();
	}

	function get_message() {
		return sprintf( '%s You received %u credits!', $this->reason, $this->credits );
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

	public function rewind( array $params ) {
		$this->player->award_credits( - $this->credits );
	}
}