<?php

require_once dirname( __FILE__ ) . '/class-model.php';

class League extends Model
{
	protected $name;
	protected $start;
	protected $end;
	protected $tournaments;
    protected $standings;

	public function save() {
		global $league_plugin;
		$league_plugin->get_leagues()->save($this);
	}

	/**
	 * @param mixed $end
	 */
	public function setEnd( $end ) {
		$this->end = $end;
	}

	/**
	 * @return mixed
	 */
	public function getEnd() {
		return $this->end;
	}

	/**
	 * @param mixed $name
	 */
	public function setName( $name ) {
		$this->name = $name;
	}

	/**
	 * @return mixed
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param mixed $start
	 */
	public function setStart( $start ) {
		$this->start = $start;
	}

	/**
	 * @return mixed
	 */
	public function getStart() {
		return $this->start;
	}

	/**
	 * @param mixed $tournaments
	 */
	public function setTournaments( $tournaments ) {
		$this->tournaments = $tournaments;
	}

	/**
	 * @return mixed
	 */
	public function getTournaments() {
		return $this->tournaments;
	}

    /**
     * @param mixed $standings
     */
    public function setStandings( $standings ) {
        $this->standings = $standings;
    }

    /**
     * @return mixed
     */
    public function getStandings() {
        return $this->standings;
    }
}