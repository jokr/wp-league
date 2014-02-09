<?php

require_once dirname( __FILE__ ) . '/class-model.php';

class League extends Model
{
	protected $name;
	protected $start;
	protected $end;
	protected $standings;

	protected $tournaments;

	public function __construct( array $properties ) {
		parent::__construct( $properties );
		$this->standings = unserialize( $properties['standings'] );
	}

	public function get_vars() {
		$result = parent::get_vars();
		unset($result['tournaments']);
		$result['standings'] = serialize( $this->standings );
		return $result;
	}

	public function save() {
		global $league_plugin;
		$league_plugin->get_leagues()->save( $this );
	}

	public function setEnd( $end ) {
		$this->end = $end;
	}

	public function getEnd() {
		return $this->end;
	}

	public function setName( $name ) {
		$this->name = $name;
	}

	public function getName() {
		return $this->name;
	}

	public function setStart( $start ) {
		$this->start = $start;
	}

	public function getStart() {
		return $this->start;
	}

	public function setTournaments( $tournaments ) {
		$this->tournaments = $tournaments;
	}

	public function getTournaments() {
		return $this->tournaments;
	}

	public function setStandings( $standings ) {
		$this->standings = $standings;
	}

	public function get_standings() {
		return $this->standings;
	}

	public function award_league_points( Player $player, $points, $winner ) {
		if ( isset($this->standings[$player->get_id()]) ) {
			$this->standings[$player->get_id()]['points'] += $points;
			if ( $winner ) {
				$this->standings[$player->get_id()]['wins'] ++;
			}
		}
	}

	public function deduct_league_points( $playerId, $points, $win = false ) {
		if ( $standing = $this->standings[$playerId] ) {
			$standing['points'] -= $points;
			if ( $win ) {
				$standing['wins'] --;
			}
		}
	}

	public function add_player_to_league( Player $player ) {
		if ( ! isset($this->standings[$player->get_id()]) ) {
			$this->standings[$player->get_id()] = array(
				'points' => 0,
				'wins' => 0,
				'participation' => 1
			);
		} else {
			$this->standings[$player->get_id()]['participation'] ++;
		}
	}

	public function remove_player_from_league( $playerId ) {
		if ( isset($this->standings[$playerId]) ) {
			$this->standings[$playerId]['participation'] --;
		}
	}

	public function get_tournaments() {
		if ( ! $this->tournaments ) {
			global $league_plugin;
			$this->tournaments = $league_plugin->get_tournaments()->get_by_league( $this->id );
		}
		return $this->tournaments;
	}
}