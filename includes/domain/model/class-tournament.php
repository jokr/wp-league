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
        $this->status = 'CLOSED';
    }

    public function get_vars() {
        $result = parent::get_vars();
        unset( $result['matches'] );
        $result['standings'] = serialize( $this->standings );
        return $result;
    }

	public function save() {
		global $league_plugin;
		$league_plugin->get_tournaments()->save($this);
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

    public function getStatus() {
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

    public function getStandings() {
        return $this->standings;
    }

	public function setXml( $xml ) {
		$this->xml = $xml;
	}

	public function getXml() {
		return $this->xml;
	}
}