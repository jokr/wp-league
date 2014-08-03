<?php

require_once LEAGUE_PLUGIN_DIR . 'includes/domain/model/class-model.php';

abstract class League_Event extends Model
{
	protected $player;

	public function __construct( Player $player ) {
		$this->player = $player;
	}

	public abstract function apply();

	public function has_been_applied() {
		return isset( $this->id );
	}

	public function get_vars() {
		return array(
			'id' => $this->get_id(),
			'date' => $this->get_date(),
			'type' => $this->get_type(),
			'player_id' => $this->get_player()->get_id(),
			'params' => serialize( $this->get_params() )
		);
	}

	public function get_player() {
		return $this->player;
	}

	public abstract function get_date();

	public abstract function get_message();

	public abstract function get_type();

	public abstract function get_params();

	public abstract function rewind();
}