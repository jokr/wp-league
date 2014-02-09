<?php

require_once LEAGUE_PLUGIN_DIR . 'includes/domain/model/class-model.php';

abstract class League_Event extends Model
{
	protected $player;

	public function __construct(Player $player) {
		$this->player = $player;
	}

	public final function apply() {
		$this->_apply();
		$this->save();
	}

	protected abstract function _apply();

	public abstract function rewind(array $params);

	public function has_been_applied() {
		return isset($this->id);
	}

	public function save() {
		global $league_plugin;
		$league_plugin->get_events()->save($this);
	}

	public function get_vars() {
		return array(
			'id' => $this->get_id(),
			'date' => $this->get_date(),
			'type' => $this->get_type(),
			'player_id' => $this->get_player()->get_id(),
			'message' => $this->get_message(),
			'params' => serialize($this->get_params())
		);
	}

	public function get_player() {
		return $this->player;
	}

	public abstract function get_date();

	public abstract function get_message();

	public abstract function get_type();

	public abstract function get_params();
}