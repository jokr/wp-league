<?php

require_once dirname( __FILE__ ) . '/class-league-event.php';

class Credit_Points extends League_Event
{
	private $credits;
	private $message;
	private $date;

	public function __construct( Player $player, $credits, $message, $date ) {
		parent::__construct( $player );
		$this->credits = $credits;
		$this->message = $message;
		$this->date = $date;
	}

	public function apply() {
		assert( ! isset( $this->id ) );
		$this->player->award_credits( $this->credits );
	}

	function get_message() {
		return $this->message;
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
			'credits' => $this->credits,
			'message' => $this->message,
		);
	}

	public function rewind() {
		assert( isset( $this->id ) );
		$this->player->award_credits( - $this->credits );
	}
}