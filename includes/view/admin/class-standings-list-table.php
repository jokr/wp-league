<?php

require_once LEAGUE_PLUGIN_DIR . 'includes/view/class-list-table.php';

class Standings_List_Table extends List_Table
{
	private $tournament;
	private $players;
	private $rules;
	private $events;

	public function __construct( Tournament $tournament, League_Rules $rules ) {
		$this->tournament = $tournament;
		$this->players = League_Plugin::get_instance()->get_players();
		$this->rules = $rules;
		$this->events = League_Plugin::get_instance()->get_events();
		wp_enqueue_script( 'prize-control' );
	}

	protected function get_items() {
		return $this->tournament->get_standings();
	}

	protected function get_all_columns() {
		return array(
			'player_id' => 'ID',
			'rank' => __( 'Rank', 'league' ),
			'name' => __( 'Player', 'league' ),
			'points' => __( 'Points', 'league' ),
			'credit_points' => __( 'Credits', 'league' ),
			'league_points' => __( 'League Points', 'league' ),
			'winner' => __( 'Winner', 'league' )
		);
	}

	protected function get_column_widths() {
		return array(
			'rank' => '5%',
			'name' => '30%'
		);
	}

	protected function get_hidden_columns() {
		return array('player_id');
	}

	protected function display_top_tablenav() {
		if ( $this->is_open() ) {
			submit_button( __( 'Save All', 'league' ), 'primary', 'submit', false );
			printf( '<input type="button" class="button reset-league-points" value="%s" />', __( 'Reset', 'league' ) );
			printf( '<span class="control-values">%s%s%s</span>',
				$this->disabled_number_input( __( 'Players', 'league' ), 'players', count( $this->items ) ),
				$this->disabled_number_input( __( 'Recommended Prize Pool', 'league' ), 'rec-pool',
					$this->rules->get_recommended_prize_pool() ),
				$this->disabled_number_input( __( 'Current Prize Pool', 'league' ), 'cur-pool', count( $this->items ) )
			);
		}
	}

	private function disabled_number_input( $name, $slug, $number ) {
		return sprintf( '<label for="%2$s">%1$s</label><input type="text" id="%2$s" name="%2$s" value="%3$u" disabled />',
			$name, $slug, $number );
	}

	protected function column_rank( array $standing ) {
		return sprintf( '<input type="hidden" name=players[%1$u][rank] value="%2$u" />%2$s',
			$standing['player'],
			$this->get_index() + 1
		);
	}

	protected function column_name( array $standing ) {
		$player = $this->players->get_by_id( $standing['player'] );
		return $player->getFirst() . ' ' . $player->getLast();
	}

	protected function column_credit_points( array $standing ) {
		if ( $this->is_open() ) {
			return sprintf( '<input class="credit-points" type="number" min="0" name="players[%u][credits]" value="%u"/>',
				$standing['player'],
				$this->rules->get_recommended_prize( $this->get_index() + 1 )
			);
		} else {
			return $this->events->get_credits( $this->tournament, $this->players->get_by_id( $standing['player'] ) );
		}
	}

	protected function column_league_points( array $standing ) {
		if ( $this->is_open() ) {
			return sprintf( '<input class="league-points" type="number" min="0" name="players[%u][league]" value="%u"/>',
				$standing['player'],
				$this->rules->get_recommended_league_points( $standing )
			);
		} else {
			return $this->events->get_league_points( $this->tournament, $this->players->get_by_id( $standing['player'] ) );
		}
	}

	protected function column_winner( array $standing ) {
		if ( $this->is_open() ) {
			return sprintf( '<input class="league-winner" type="checkbox" name="players[%u][winner]" %s/>',
				$standing['player'],
				checked( $this->get_index(), 0, false )
			);
		} else {
			if ( $this->events->is_winner( $this->tournament, $this->players->get_by_id( $standing['player'] ) ) ) {
				return sprintf( '<input class="league-winner" type="checkbox" name="players[%u][winner]" %s %s/>',
					$standing['player'],
					checked( true, true, false ),
					disabled( true, true, false )
				);
			} else {
				return '';
			}
		}
	}

	private function is_open() {
		return 'FINISHED' === $this->tournament->get_status();
	}
}