<?php

require_once LEAGUE_PLUGIN_DIR . 'includes/view/class-list-table.php';

class Match_List_Table extends List_Table
{
	private $tournament;
	private $players;
	private $playerCache;

	public function __construct( Tournament $tournament, Player_Service $players ) {
		$this->tournament = $tournament;
		$this->players = $players;
		$this->playerCache = array();
	}

	protected function get_items() {
		return $this->tournament->get_matches();
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
		return array( 'round' );
	}

	protected function sort() {
		usort( $this->items, function ( Match $a, Match $b ) {
			return $a->get_round() - $b->get_round();
		} );
	}

	protected function get_top_tablenav() {
		return sprintf( '
		<input type="button" class="button button-red" id="delete-results"
		       value="%s"/>
		<dialog id="delete-confirm-window">
			<div>
				<h3>%s</h3>
				<p>%s</p>
				<form name="delete-results" method="post" action="%s">
					<input type="hidden" name="action" value="delete_results"/>
					<input type="hidden" name="id" value="%s"/>
					%s
					<input type="submit" class="button button-primary" id="delete-confirm"
					       value="%s"/>
					<input type="button" class="button button-secondary" id="delete-cancel"
					       value="%s"/>
				</form>
			</div>
		</dialog>
	', __( 'Delete Results', 'league' ), __( 'Are you sure?', 'league' ),
			__( 'Deleting the results will rewind all points gained from this tournament and delete all matches.', 'league' ),
			admin_url( 'admin-post.php' ),
			esc_attr( $this->tournament->get_id() ),
			wp_nonce_field( 'delete-results', '_wpnonce_delete_results', true, false ),
			__( 'Yes, I am sure.', 'league' ),
			__( 'No, cancel.', 'league' )
		);
	}

	private function get_player( $id ) {
		if ( ! isset( $player[$id] ) ) {
			$player = $this->players->get_by_id( $id );
			$player = $player->get_full_name();
			$this->playerCache[$id] = $player;
		} else {
			$player = $this->playerCache[$id];
		}

		return $player;
	}

	protected function column_player( Match $match ) {
		return $this->get_player( $match->get_player_id() );
	}

	protected function column_opponent( Match $match ) {
		if ( $match->has_opponent() ) {
			return $this->get_player( $match->get_opponent_id() );
		} else {
			return '';
		}
	}

	protected function column_result( Match $match ) {
		if ( 3 == $match->get_outcome() ) {
			return __( 'Bye', 'league' );
		} elseif ( 5 == $match->get_outcome() ) {
			return __( 'Match Loss', 'league' );
		} else {
			return $match->get_wins() . ' - ' . $match->get_losses() . ' - ' . $match->get_draws();
		}
	}

	protected function column_round( Match $match ) {
		return __( 'Round', 'league' ) . ' ' . $match->get_round();
	}
}