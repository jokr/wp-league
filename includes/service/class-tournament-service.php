<?php

class Tournament_Service
{
	private $tournaments;

	public function __construct( Tournaments $tournaments ) {
		$this->tournaments = $tournaments;
	}

	public function get_all() {
		return $this->tournaments->get_all();
	}

	public function get_by_id( $id ) {
		return $this->tournaments->get_by_id( $id );
	}

	public function get_all_for_league( $id ) {
		return $this->tournaments->get_by_league( $id );
	}

	public function create( $league_id, $date, $format, $url ) {
		$tournament = new Tournament( $league_id, $date, $format );
		$tournament->set_url( $url );
		$this->tournaments->save( $tournament );
		return $tournament;
	}

	public function update( $id, $league_id, $date, $format, $url ) {
		$tournament = $this->tournaments->get_by_id( $id );
		if ( isset( $tournament ) ) {
			$tournament->set_league_id( $league_id );
			$tournament->set_date( $date );
			$tournament->set_format( $format );
			$tournament->set_url( $url );
			$this->tournaments->save( $tournament );
		} else {
			return false;
		}
		return true;
	}

	public function exists( $id ) {
		return $this->tournaments->exists( $id );
	}
}