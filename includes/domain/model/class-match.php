<?php

require_once dirname( __FILE__ ) . '/class-model.php';

class Match extends Model
{
	protected $tournament_id;
	protected $round;
	protected $date;
	protected $player_id;
	protected $opponent_id;
	protected $outcome;
	protected $wins;
	protected $losses;
	protected $draws;

	public function save() {
		global $league_plugin;
		$league_plugin->get_matches()->save( $this );
	}

	/**
	 * @param mixed $round
	 */
	public function setRound( $round ) {
		$this->round = $round;
	}

	/**
	 * @return mixed
	 */
	public function getRound() {
		return $this->round;
	}

	/**
	 * @param mixed $date
	 */
	public function setDate( $date ) {
		$this->date = $date;
	}

	/**
	 * @return mixed
	 */
	public function getDate() {
		return $this->date;
	}

	/**
	 * @param mixed $draws
	 */
	public function setDraws( $draws ) {
		$this->draws = $draws;
	}

	/**
	 * @return mixed
	 */
	public function getDraws() {
		return $this->draws;
	}

	/**
	 * @param mixed $losses
	 */
	public function setLosses( $losses ) {
		$this->losses = $losses;
	}

	/**
	 * @return mixed
	 */
	public function getLosses() {
		return $this->losses;
	}

	/**
	 * @param mixed $opponent_id
	 */
	public function setOpponentId( $opponent_id ) {
		$this->opponent_id = $opponent_id;
	}

	/**
	 * @return mixed
	 */
	public function getOpponentId() {
		return $this->opponent_id;
	}

	/**
	 * @param mixed $outcome
	 */
	public function setOutcome( $outcome ) {
		$this->outcome = $outcome;
	}

	/**
	 * @return mixed
	 */
	public function getOutcome() {
		return $this->outcome;
	}

	/**
	 * @param mixed $player_id
	 */
	public function setPlayerId( $player_id ) {
		$this->player_id = $player_id;
	}

	/**
	 * @return mixed
	 */
	public function getPlayerId() {
		return $this->player_id;
	}

	/**
	 * @param mixed $tournament_id
	 */
	public function setTournamentId( $tournament_id ) {
		$this->tournament_id = $tournament_id;
	}

	/**
	 * @return mixed
	 */
	public function getTournamentId() {
		return $this->tournament_id;
	}

	/**
	 * @param mixed $wins
	 */
	public function setWins( $wins ) {
		$this->wins = $wins;
	}

	/**
	 * @return mixed
	 */
	public function getWins() {
		return $this->wins;
	}

	public function has_opponent() {
		return isset($this->opponent_id);
	}
}