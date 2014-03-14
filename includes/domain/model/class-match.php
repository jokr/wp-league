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

	public function __construct( $tournament_id, $round, $date, $player_id, $opponent_id, $outcome, $wins, $losses, $draws ) {
		$this->tournament_id = $tournament_id;
		$this->round = $round;
		$this->date = $date;
		$this->player_id = $player_id;
		$this->opponent_id = $opponent_id;
		$this->outcome = $outcome;
		$this->wins = $wins;
		$this->losses = $losses;
		$this->draws = $draws;
	}

	/**
	 * @param mixed $round
	 */
	public function set_round( $round ) {
		$this->round = $round;
	}

	/**
	 * @return mixed
	 */
	public function get_round() {
		return $this->round;
	}

	/**
	 * @param mixed $date
	 */
	public function set_date( $date ) {
		$this->date = $date;
	}

	/**
	 * @return mixed
	 */
	public function get_date() {
		return $this->date;
	}

	/**
	 * @param mixed $draws
	 */
	public function set_draws( $draws ) {
		$this->draws = $draws;
	}

	/**
	 * @return mixed
	 */
	public function get_draws() {
		return $this->draws;
	}

	/**
	 * @param mixed $losses
	 */
	public function set_losses( $losses ) {
		$this->losses = $losses;
	}

	/**
	 * @return mixed
	 */
	public function get_losses() {
		return $this->losses;
	}

	/**
	 * @param mixed $opponent_id
	 */
	public function set_opponent_id( $opponent_id ) {
		$this->opponent_id = $opponent_id;
	}

	/**
	 * @return mixed
	 */
	public function get_opponent_id() {
		return $this->opponent_id;
	}

	/**
	 * @param mixed $outcome
	 */
	public function set_outcome( $outcome ) {
		$this->outcome = $outcome;
	}

	/**
	 * @return mixed
	 */
	public function get_outcome() {
		return $this->outcome;
	}

	/**
	 * @param mixed $player_id
	 */
	public function set_player_id( $player_id ) {
		$this->player_id = $player_id;
	}

	/**
	 * @return mixed
	 */
	public function get_player_id() {
		return $this->player_id;
	}

	/**
	 * @param mixed $tournament_id
	 */
	public function set_tournament_id( $tournament_id ) {
		$this->tournament_id = $tournament_id;
	}

	/**
	 * @return mixed
	 */
	public function get_tournament_id() {
		return $this->tournament_id;
	}

	/**
	 * @param mixed $wins
	 */
	public function set_wins( $wins ) {
		$this->wins = $wins;
	}

	/**
	 * @return mixed
	 */
	public function ge_wWins() {
		return $this->wins;
	}

	public function has_opponent() {
		return isset( $this->opponent_id );
	}
}