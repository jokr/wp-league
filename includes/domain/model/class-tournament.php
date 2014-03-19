<?php

require_once dirname( __FILE__ ) . '/class-model.php';

class Tournament extends Model
{
	protected $league_id;
	protected $date;
	protected $format;
	protected $status;
	protected $url;
	protected $matches;
	protected $standings;
	protected $xml;

	function __construct( $league_id, $date, $format ) {
		$this->league_id = $league_id;
		$this->format = $format;
		$this->date = $date;
		$this->matches = array();
		$this->status = 'OPEN';
		$this->standings = array();
	}

	public static function from_array( $id, array $array ) {
		$result = new Tournament( $array['league_id'], $array['date'], $array['format'] );
		$result->set_id( $id );
		$result->set_status( $array['status'] );
		$result->set_url( $array['url'] );
		$result->set_standings( unserialize( $array['standings'] ) );
		$result->set_xml( $array['xml'] );
		return $result;
	}

	public function add_results( $xml, $standings ) {
		$this->xml = $xml;
		$this->standings = $standings;
		$this->status = 'FINISHED';
	}

	public function get_vars() {
		$result = parent::get_vars();
		unset( $result['matches'] );
		if ( isset( $result['standings'] ) ) {
			$result['standings'] = serialize( $this->standings );
		} else {
			$result['standings'] = null;
		}
		if ( ! isset( $result['xml'] ) ) {
			$result['xml'] = null;
		}
		return $result;
	}

	public function set_date( $date ) {
		$this->date = $date;
	}

	public function get_date() {
		return $this->date;
	}

	public function set_format( $format ) {
		$this->format = $format;
	}

	public function get_format() {
		return $this->format;
	}

	public function set_league_id( $league_id ) {
		$this->league_id = $league_id;
	}

	public function get_league_id() {
		return $this->league_id;
	}

	public function set_status( $status ) {
		$this->status = $status;
	}

	public function get_status() {
		return $this->status;
	}

	public function set_url( $url ) {
		$this->url = $url;
	}

	public function get_url() {
		return $this->url;
	}

	public function set_matches(array $matches) {
		$this->matches = $matches;
	}

	public function get_matches() {
		return $this->matches;
	}

	public function set_standings( $standings ) {
		$this->standings = $standings;
	}

	public function get_standings() {
		return $this->standings;
	}

	public function set_xml( $xml ) {
		$this->xml = $xml;
	}

	public function get_xml() {
		return $this->xml;
	}

	public function delete_results() {
		$this->xml = null;
		$this->standings = null;
		$this->matches = null;
		$this->status = 'WAITING';
	}

	public function set_winner( $winner_id ) {
		if ( $this->get_winner() != $winner_id ) {
			$rank = $this->find_in_standings( $winner_id );
			if ( isset( $rank ) ) {
				$tmp = $this->standings[$rank];
				$this->standings[$rank] = $this->standings[1];
				$this->standings[1] = $tmp;
			}
		}
	}

	private function find_in_standings( $id ) {
		foreach ( $this->standings as $rank => $standing ) {
			if ( $standing['player'] == $id ) {
				return $rank;
			}
		}
		return null;
	}

	public function get_winner() {
		return $this->standings[1]['player'];
	}
}