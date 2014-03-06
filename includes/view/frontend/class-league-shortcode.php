<?php

class League_Shortcode
{
	private $leagues;
	private $players;
	private $params;
	private $errors;

	public function __construct( League_Service $leagues, Player_Service $players ) {
		$this->leagues = $leagues;
		$this->players = $players;
		$this->params = array( 'id' => null );
		$this->errors = array();
	}

	public function render( $atts ) {
		$this->validate_args( $atts );

		if ( $this->has_errors() ) {
			return $this->display_errors();
		} else {
			wp_enqueue_script(
				'league-match-details',
				LEAGUE_PLUGIN_URL . 'js/match-details.js',
				array( 'jquery' ),
				LEAGUE_PLUGIN_VERSION
			);

			wp_enqueue_style(
				'league-match-details',
				LEAGUE_PLUGIN_URL . 'css/match-details.css',
				array(),
				LEAGUE_PLUGIN_VERSION
			);

			return $this->display();
		}
	}

	public function validate_args( $atts ) {
		$this->params = shortcode_atts( $this->params, $atts, 'league' );

		if ( ! isset( $this->params['id'] ) || ! is_numeric( $this->params['id'] ) || ! $this->leagues->exists( $this->params['id'] ) ) {
			array_push( $this->errors, sprintf( "<p><b>%s</b></p>", __( 'Invalid league id.', 'league' ) ) );
		}
	}

	public function display() {
		require_once LEAGUE_PLUGIN_DIR . 'includes/view/frontend/class-league-schedule-list.php';
		require_once LEAGUE_PLUGIN_DIR . 'includes/view/frontend/class-league-scoreboard-list.php';

		$league = $this->leagues->get_by_id( $this->params['id'] );

		$schedule = new League_Schedule_List( $league, $this->players );
		$schedule->prepare_items();

		$scoreboard = new League_Scoreboard_List( $league, $this->players );
		$scoreboard->prepare_items();

		return sprintf( '<div><h2>%s</h2>%s</div><div><h2>%s</h2>%s</div>',
			__( 'Schedule', 'league' ), $schedule->display( false, false ),
			__( 'Scoreboard', 'league' ), $scoreboard->display( false, false )
		);
	}

	public function display_errors() {
		return implode( $this->errors );
	}

	private function has_errors() {
		return ! empty( $this->errors );
	}
}