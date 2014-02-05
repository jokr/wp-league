<?php

class League_Shortcode
{
	private $leagues;
	private $tournaments;
	private $params;
	private $errors;

	public static function render( $atts ) {
		global $league_plugin;
		$shortcode = new League_Shortcode($league_plugin->get_leagues(), $league_plugin->get_tournaments());

		$shortcode->validate_args( $atts );
		if ( $shortcode->has_errors() ) {
			return $shortcode->display_errors();
		} else {
			return $shortcode->display();
		}
	}

	public function __construct( Leagues $leagues, Tournaments $tournaments ) {
		$this->leagues = $leagues;
		$this->tournaments = $tournaments;
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
		$tournaments = $this->tournaments->get_by_league( $this->params['id'] );
		usort( $tournaments, function ( Tournament $a, Tournament $b ) {
			return strtotime( $a->getDate() ) - strtotime( $b->getDate() );
		} );

		$result = '<h2>Tournament schedule</h2>';
		$result .= '<table class="league-schedule">';
		$result .= '<thead><th>Date</th><th>Format</th><th>Winner</th><th>Players</th></thead>';
		$result .= '<tbody>';
		foreach ( $tournaments as $tournament ) {
			$result .= $this->display_tournament( $tournament );
		}
		$result .= '</tbody></table>';
		return $result;
	}

	public function display_errors() {
		return implode( '', $this->errors );
	}

	private function display_tournament( Tournament $tournament ) {
		$date = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $tournament->getDate() ) );
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

	private function has_errors() {
		return ! empty($this->errors);
	}
}