<?php

require_once dirname( __FILE__ ) . '/class-model.php';

class Player extends Model
{
	protected $first;
	protected $last;
	protected $dci;
	protected $credits;
	protected $wp_user_id;

	public function save() {
        League_Plugin::get_instance()->get_players()->save( $this );
	}

	/**
	 * Adds a amount of credits to the player and saves it to the database.
	 * @param $credits int amount of credits to be added.
	 */
	public function award_credits( $credits ) {
		if ( is_numeric( $credits ) ) {
			$this->credits += $credits;
		}
	}

	public function setDci( $dci ) {
		$this->dci = $dci;
	}

	public function getDci() {
		return $this->dci;
	}

	public function setFirst( $first ) {
		$this->first = $first;
	}

	public function getFirst() {
		return $this->first;
	}

	public function setLast( $last ) {
		$this->last = $last;
	}

	public function getLast() {
		return $this->last;
	}

	public function setCredits( $credits ) {
		$this->credits = $credits;
	}

	public function getCredits() {
		return $this->credits;
	}

	public function setWpUserId( $wp_user_id ) {
		$this->wp_user_id = $wp_user_id;
	}

	public function getWpUserId() {
		return $this->wp_user_id;
	}

	public function get_full_name() {
		return $this->first . ' ' . $this->last;
	}
}