<?php

class Tournament_Service
{
	private $tournaments;

	public function __construct( Tournaments $tournaments ) {
		$this->tournaments = $tournaments;
	}

	public function get_by_id( $id ) {
		return $this->tournaments->get_by_id( $id );
	}

	public function get_all_for_league( $id ) {
		return $this->tournaments->get_by_league( $id );
	}
}