<?php

class Player_Service
{
	private $players;
	private $events;

	function __construct( Players $players, Event_Service $events ) {
		$this->players = $players;
		$this->events = $events;
	}

	public function get_by_id( $id ) {
		return $this->players->get_by_id( $id );
	}

	public function get_all() {
		return $this->players->get_all();
	}

	public function exists( $id ) {
		return $this->players->exists( $id );
	}

	public function add_credit_points( $player_id, $amount, $message ) {
		$player = $this->players->get_by_id( $player_id );
		$this->events->credit_event( $player, $amount, $message );
		$this->players->save( $player );
	}
}