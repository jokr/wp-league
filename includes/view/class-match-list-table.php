<?php

require_once dirname( __FILE__ ) . '/class-list-table.php';

class Match_List_Table extends List_Table
{
	private $matches;
	private $players;
	private $playerCache;

	public function __construct( array $matches ) {
		global $league_plugin;
		$this->matches = $matches;
		$this->players = $league_plugin->get_players();
		$this->playerCache = array();
	}

	protected function get_items() {
		return $this->matches;
	}

	protected function get_all_columns() {
		return array(
			'id' => 'id',
			'round' => 'round',
			'player' => __( 'Player', 'league' ),
			'opponent' => __( 'Opponent', 'league' ),
			'result' => __( 'Result', 'league' )
		);
	}

	protected function get_grouped_columns() {
		return array('round');
	}

	protected function sort() {
		usort( $this->items, function ( Match $a, Match $b ) {
			return $a->getRound() - $b->getRound();
		} );
	}

	private function get_player( $id ) {
		if ( ! isset($player[$id]) ) {
			$player = $this->players->get_by_id( $id );
			$player = $player->getFirst() . ' ' . $player->getLast();
			$this->playerCache[$id] = $player;
		} else {
			$player = $this->playerCache[$id];
		}

		return $player;
	}

	protected function column_player( Match $match ) {
		return $this->get_player( $match->getPlayerId() );
	}

	protected function column_opponent( Match $match ) {
		return $this->get_player( $match->getOpponentId() );
	}

	protected function column_result( Match $match ) {
		return $match->getWins() . ' - ' . $match->getLosses() . ' - ' . $match->getDraws();
	}

	protected function column_round( Match $match ) {
		return __('Round', 'league') . ' ' . $match->getRound();
	}
}