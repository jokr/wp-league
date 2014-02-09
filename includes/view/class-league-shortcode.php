<?php

class League_Shortcode
{
	private $leagues;
	private $tournaments;
	private $params;
	private $errors;

	public static function render( $atts ) {
		global $league_plugin;
		$shortcode = new League_Shortcode($league_plugin->get_leagues(), $league_plugin->get_tournaments(), $league_plugin->get_players());

		$shortcode->validate_args( $atts );
		if ( $shortcode->has_errors() ) {
			return $shortcode->display_errors();
		} else {
			return $shortcode->display();
		}
	}

	public function __construct( Leagues $leagues, Tournaments $tournaments, Players $players ) {
		$this->leagues = $leagues;
		$this->tournaments = $tournaments;
		$this->players = $players;
		$this->params = array('id' => null);
		$this->errors = array();
	}

	public function validate_args( $atts ) {
		$this->params = shortcode_atts( $this->params, $atts, 'league' );

		if ( ! isset($this->params['id']) || ! is_numeric( $this->params['id'] ) || ! $this->leagues->exists( $this->params['id'] ) ) {
			array_push( $this->errors, sprintf( "<p><b>%s</b></p>", __( 'Invalid league id.', 'league' ) ) );
		}
	}

	public function display() {
		$league = $this->leagues->get_by_id( $this->params['id'] );
		$result = $this->display_schedule( $league ) . $this->display_standings( $league );
		return $result;
	}

	public function display_errors() {
		return implode( '', $this->errors );
	}

	private function display_schedule( League $league ) {
		$tournaments = $this->tournaments->get_by_league( $this->params['id'] );

		return sprintf( '
			<h2>%s: %s</h2>
			<table class="league league-schedule">
			<thead>
			<tr>
				<th>%s</th>
				<th>%s</th>
				<th>%s</th>
				<th>%s</th>
			</tr>
			</thead>
			<tbody>
				%s
			</tbody>
			</table>
		', __( 'Schedule', 'league' ), $league->getName(),
			__( 'Date', 'league' ),
			__( 'Format', 'league' ),
			__( 'Winner', 'league' ),
			__( 'Players', 'league' ),
			$this->get_schedule_rows( $tournaments )
		);
	}

	private function get_schedule_rows( array $tournaments ) {
		usort( $tournaments, function ( Tournament $a, Tournament $b ) {
			return strtotime( $a->getDate() ) - strtotime( $b->getDate() );
		} );

		$result = '';
		foreach ( $tournaments as $tournament ) {
			$result .= $this->display_tournament( $tournament );
		}
		return $result;
	}

	private function display_standings( League $league ) {
		return sprintf( '<h2>%s: %s</h2>
		<table class="league league-standings">
			<thead>
				<th>%s</th>
				<th>%s</th>
				<th>%s</th>
				<th>%s</th>
				<th>%s</th>
			</thead>
			<tbody>
				%s
			</tbody>
		</table>',
			__( 'Scoreboard', 'league' ),
			$league->getName(),
			__( 'Rank', 'league' ),
			__( 'Player', 'league' ),
			__( 'Points', 'league' ),
			__( 'Wins', 'league' ),
			__( 'Events', 'league' ),
			$this->get_standings_rows( $league )
		);
	}

	private function display_tournament( Tournament $tournament ) {
		$date = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $tournament->getDate() ) );

		if ( 'CLOSED' == $tournament->getStatus() ) {
			$winner = $this->players->get_by_id($tournament->get_winner());
			return sprintf( '<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>',
				esc_html( $date ),
				esc_html( $tournament->getFormat() ),
				esc_html( $winner->get_full_name() ),
				esc_html( count( $tournament->getStandings() ) )
			);
		} else {
			if ( strlen( $tournament->getUrl() ) > 0 ) {
				return sprintf( '<tr><td><a href="%s">%s</a></td><td>%s</td><td></td><td></td></tr>',
					esc_url( $tournament->getUrl() ),
					esc_html( $date ),
					esc_html( $tournament->getFormat() )
				);
			} else {
				return sprintf( '<tr><td>%s</td><td>%s</td><td></td><td></td></tr>',
					esc_html( $date ),
					esc_html( $tournament->getFormat() )
				);
			}
		}
	}

	private function has_errors() {
		return ! empty($this->errors);
	}

	private function get_standings_rows( League $league ) {
		$result = '';
		$standings = $league->getStandings();
		uasort( $standings, function ( $a, $b ) {
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

		$rank = 1;
		foreach ( $standings as $id => $standing ) {
			$player = $this->players->get_by_id( $id );
			$result .= sprintf( '
				<tr>
					<td>%u</td>
					<td>%s</td>
					<td>%u</td>
					<td>%u</td>
					<td>%u</td>
				</tr>
			',
				$rank ++,
				$player->get_full_name(),
				$standing['points'],
				$standing['wins'],
				$standing['participation']
			);
		}

		return $result;
	}
}