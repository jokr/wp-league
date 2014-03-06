<?php

class Player_Service
{
	private $players;

	function __construct( Players $players ) {
		$this->players = $players;
	}

	public function get_by_id( $id ) {
		return $this->players->get_by_id( $id );
	}
}