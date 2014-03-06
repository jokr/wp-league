<?php

require_once dirname( __FILE__ ) . '/class-model.php';

class League extends Model
{
    protected $name;
    protected $start;
    protected $end;
    protected $standings;
	private $tournaments;

    public function __construct( $name, $start, $end, $standings = array() ) {
        $this->name = $name;
        $this->start = $start;
        $this->end = $end;
        $this->standings = $standings;
		$this->tournaments = array();
    }

    public function get_vars() {
        $result = parent::get_vars();
        unset( $result['tournaments'] );
        $result['standings'] = serialize( $this->standings );
        return $result;
    }

    public function set_end( $end ) {
        $this->end = $end;
    }

    public function get_end() {
        return $this->end;
    }

    public function set_name( $name ) {
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

    public function setStandings( $standings ) {
        $this->standings = $standings;
    }

    public function get_standings() {
        return $this->standings;
    }

	public function set_tournaments(array $tournaments) {
		$this->tournaments = $tournaments;
	}

	public function get_tournaments() {
		return $this->tournaments;
	}

    public function add_player( $player_id ) {
        if (! isset( $this->standings[$player_id] )) {
            $this->standings[$player_id] = array(
                'points' => 0,
                'wins' => 0,
                'participation' => 1
            );
        } else {
            $this->standings[$player_id]['participation'] ++;
        }
    }

    public function remove_player( $player_id ) {
        if (isset( $this->standings[$player_id] )) {
            $this->standings[$player_id]['participation'] --;
        }
    }

    public function add_league_points( $player_id, $points, $winner ) {
        if (isset( $this->standings[$player_id] )) {
            $this->standings[$player_id]['points'] += $points;
            if ($winner) {
                $this->standings[$player_id]['wins'] ++;
            }
        }
    }

    public function remove_league_points( $player_id, $points, $winner ) {
        if ($standing = $this->standings[$player_id]) {
            $standing['points'] -= $points;
            if ($winner) {
                $standing['wins'] --;
            }
        }
    }
}