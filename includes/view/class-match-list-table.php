<?php

require_once dirname( __FILE__ ) . '/class-list-table.php';

class Match_List_Table extends List_Table
{
	private $tournament;
	private $matches;
	private $players;
	private $playerCache;

	public function __construct( Tournament $tournament ) {
		global $league_plugin;
		$this->tournament = $tournament;
		$this->matches = $tournament->getMatches();
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

	protected function display_top_tablenav() {
		?>
		<input type="button" class="button button-red" id="delete-results"
		       value="<?php _e( 'Delete Results', 'league' ); ?>"/>
		<dialog id="delete-confirm-window">
			<div>
				<h3><?php _e( 'Are you sure?', 'league' ); ?></h3>

				<p><?php _e( 'Deleting the results will rewind all points gained from this tournament and delete all matches.',
						'league' ); ?></p>

				<form name="delete-results" method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>">
					<input type="hidden" name="action" value="delete_results"/>
					<input type="hidden" name="id" value="<?php echo esc_attr( $this->tournament->get_id() ); ?>"/>
					<?php wp_nonce_field( 'delete-results', '_wpnonce_delete_results' ); ?>
					<input type="submit" class="button button-primary" id="delete-confirm"
					       value="<?php _e( 'Yes, I am sure.', 'league' ); ?>"/>
					<input type="button" class="button button-secondary" id="delete-cancel"
					       value="<?php _e( 'No, cancel.', 'league' ); ?>"/>
				</form>
			</div>
		</dialog>
	<?php
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
		if ( $match->has_opponent() ) {
			return $this->get_player( $match->getOpponentId() );
		} else {
			return '';
		}
	}

	protected function column_result( Match $match ) {
		if ( 3 == $match->getOutcome() ) {
			return __( 'Bye', 'league' );
		} elseif ( 5 == $match->getOutcome() ) {
			return __( 'Match Loss', 'league' );
		} else {
			return $match->getWins() . ' - ' . $match->getLosses() . ' - ' . $match->getDraws();
		}
	}

	protected function column_round( Match $match ) {
		return __( 'Round', 'league' ) . ' ' . $match->getRound();
	}
}