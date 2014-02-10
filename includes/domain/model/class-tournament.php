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

	public function add_results( $xml, $standings ) {
		$this->xml = $xml;
		$this->standings = $standings;
		$this->status = 'FINISHED';
	}

	public function get_vars() {
		$result = parent::get_vars();
		unset($result['matches']);
		if ( isset($result['standings']) ) {
			$result['standings'] = serialize( $this->standings );
		} else {
			$result['standings'] = null;
		}
		if ( ! isset($result['xml']) ) {
			$result['xml'] = null;
		}
		return $result;
	}

	public function save() {
        League_Plugin::get_instance()->get_tournaments()->save( $this );
	}

	public function setDate( $date ) {
		$this->date = $date;
	}

	public function getDate() {
		return $this->date;
	}

	public function setFormat( $format ) {
		$this->format = $format;
	}

	public function getFormat() {
		return $this->format;
	}

	public function setLeagueId( $league_id ) {
		$this->league_id = $league_id;
	}

	public function getLeagueId() {
		return $this->league_id;
	}

	public function setStatus( $status ) {
		$this->status = $status;
	}

	public function get_status() {
		return $this->status;
	}

	public function setUrl( $url ) {
		$this->url = $url;
	}

	public function getUrl() {
		return $this->url;
	}

	public function getMatches() {
		return $this->matches;
	}

	public function setStandings( $standings ) {
		$this->standings = $standings;
	}

	public function get_standings() {
		return $this->standings;
	}

	public function setXml( $xml ) {
		$this->xml = $xml;
	}

	public function getXml() {
		return $this->xml;
	}

	public function get_league() {
		return League_Plugin::get_instance()->get_leagues()->get_by_id( $this->getLeagueId() );
	}

	public function delete_results() {
		$this->xml = NULL;
		$this->standings = NULL;
		$this->matches = NULL;
		$this->status = 'WAITING';
	}

	public function set_winner( $winner_id ) {
		if ( $this->get_winner() != $winner_id ) {
			$rank = $this->find_in_standings( $winner_id );
			if ( isset($rank) ) {
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