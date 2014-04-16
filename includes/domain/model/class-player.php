<?php

require_once dirname( __FILE__ ) . '/class-model.php';

class Player extends Model
{
	protected $first;
	protected $last;
	protected $dci;
	protected $credits;
	protected $wp_user_id;
	protected $secret;

	public static function from_array( $id, array $array ) {
		$result = new Player( $array['first'], $array['last'], $array['dci'] );
		$result->set_id( (int)$id );
		$result->set_credits( (int)$array['credits'] );
		$result->set_wp_user_id( (int)$array['wp_user_id'] );
		$result->set_secret( $array['secret'] );
		return $result;
	}

	public function __construct( $first, $last, $dci ) {
		$this->first = $first;
		$this->last = $last;
		$this->dci = $dci;
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

	public function set_dci( $dci ) {
		$this->dci = $dci;
	}

	public function get_dci() {
		return $this->dci;
	}

	public function set_first( $first ) {
		$this->first = $first;
	}

	public function get_first() {
		return $this->first;
	}

	public function set_last( $last ) {
		$this->last = $last;
	}

	public function get_last() {
		return $this->last;
	}

	public function set_credits( $credits ) {
		$this->credits = $credits;
	}

	public function get_credits() {
		return $this->credits;
	}

	public function set_wp_user_id( $wp_user_id ) {
		$this->wp_user_id = $wp_user_id;
	}

	public function get_wp_user_id() {
		return $this->wp_user_id;
	}

	public function get_secret() {
		return $this->secret;
	}

	public function set_secret( $secret ) {
		$this->secret = $secret;
	}

	public function get_full_name() {
		return $this->first . ' ' . $this->last;
	}
}