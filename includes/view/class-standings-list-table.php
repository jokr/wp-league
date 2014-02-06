<?php

require_once dirname( __FILE__ ) . '/class-list-table.php';

class Standings_List_Table extends List_Table
{
	private $tournament;
	private $players;

	public function __construct( Tournament $tournament ) {
		global $league_plugin;
		$this->tournament = $tournament;
		$this->players = $league_plugin->get_players();
	}

	protected function get_items() {
		return $this->tournament->getStandings();
	}

	protected function get_all_columns() {
		return array(
			'player_id' => 'ID',
			'rank' => __( 'Rank', 'league' ),
			'name' => __( 'Player', 'league' ),
			'points' => __( 'Points', 'league' ),
			'credit_points' => __( 'Credits', 'league' ),
			'league_points' => __( 'League Points', 'league' )
		);
	}

	protected function get_column_widths() {
		return array(
			'rank' => '5%'
		);
	}


	protected function get_hidden_columns() {
		return array('player_id');
	}

	protected function column_rank() {
		return $this->get_index() + 1;
	}

	protected function column_name( array $standing ) {
		$player = $this->players->get_by_id( $standing['player'] );
		return $player->getFirst() . ' ' . $player->getLast();
	}
}