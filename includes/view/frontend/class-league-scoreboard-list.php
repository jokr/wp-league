<?php

include_once LEAGUE_PLUGIN_DIR . 'includes/view/class-list-table.php';

class League_Scoreboard_List extends List_Table
{
	private $league;
	private $players;

	public function __construct( League $league, Player_Service $players ) {
		$this->league = $league;
		$this->players = $players;
	}

	protected function get_items() {
		return $this->league->get_standings();
	}

	protected function get_all_columns() {
		return array(
			'rank' => __( 'Rank', 'league' ),
			'player' => __( 'Player', 'league' ),
			'points' => __( 'Points', 'league' ),
			'wins' => __( 'Wins', 'league' ),
			'participation' => __( 'Events', 'league' )
		);
	}

	protected function get_table_classes() {
		return array( 'league', 'league-scoreboard' );
	}

	protected function sort() {
		uasort( $this->items, function ( $a, $b ) {
			if ( $a['points'] == $b['points'] ) {
				if ( $a['wins'] == $b['wins'] ) {
					if ( $a['participation'] == $b['participation'] ) {
						return 0;
					} else {
						return $b['participation'] - $a['participation'];
					}
				} else {
					return $b['wins'] - $a['wins'];
				}
			} else {
				return $b['points'] - $a['points'];
			}
		} );
	}

	protected function column_rank() {
		return $this->get_index() + 1;
	}

	protected function column_player() {
		return $this->players->get_by_id( $this->get_key() )->get_full_name();
	}
}