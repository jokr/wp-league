<?php

require_once LEAGUE_PLUGIN_DIR . 'includes/domain/persistence/class-leagues.php';
require_once LEAGUE_PLUGIN_DIR . 'includes/domain/persistence/class-tournaments.php';
require_once LEAGUE_PLUGIN_DIR . 'includes/domain/persistence/class-players.php';
require_once LEAGUE_PLUGIN_DIR . 'includes/domain/persistence/class-matches.php';
require_once LEAGUE_PLUGIN_DIR . 'includes/domain/persistence/class-league-events.php';

require_once LEAGUE_PLUGIN_DIR . 'includes/service/class-league-service.php';
require_once LEAGUE_PLUGIN_DIR . 'includes/service/class-tournament-service.php';
require_once LEAGUE_PLUGIN_DIR . 'includes/service/class-player-service.php';

require_once LEAGUE_PLUGIN_DIR . 'includes/view/admin/class-league-screen.php';
require_once LEAGUE_PLUGIN_DIR . 'includes/view/admin/class-tournament-screen.php';
require_once LEAGUE_PLUGIN_DIR . 'includes/view/frontend/class-league-shortcode.php';
require_once LEAGUE_PLUGIN_DIR . 'includes/class-league-signup-page.php';

class League_Plugin
{
	const VERSION = '0.0.1';
	const PLUGIN_SLUG = 'league';

	private static $instance;

	private $leagues;
	private $tournaments;
	private $players;
	private $matches;
	private $events;

	private $league_service;
	private $tournament_service;
	private $player_service;

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new League_Plugin();
		}

		return self::$instance;
	}

	private function __construct() {
		$this->leagues = new Leagues();
		$this->tournaments = new Tournaments();
		$this->players = new Players();
		$this->matches = new Matches();
		$this->events = new League_Events();

		$this->league_service = new League_Service( $this->leagues, $this->tournaments );
		$this->tournament_service = new Tournament_Service(
			$this->leagues, $this->tournaments, $this->players, $this->matches, $this->events
		);
		$this->player_service = new Player_Service( $this->players );

		// Register activation and deactivation
		register_activation_hook( __FILE__, array( $this, 'activate' ) );

		if ( is_admin() ) {
			new League_Screen( $this->league_service, $this->tournament_service );
			Tournament_Screen::get_instance( $this->league_service, $this->tournament_service, $this->player_service );
		} else {
			add_action( 'wp_head', array( $this, 'get_ajaxurl' ) );
			new League_Signup_Page( array(
				'url' => 'league-signup',
				'pagename' => 'league-signup'
			) );
		}

		add_action( 'init', array( $this, 'init' ) );
	}

	public function init() {
		add_shortcode( 'league', array( new League_Shortcode( $this->league_service, $this->player_service ), 'render' ) );
		add_action( 'wp_ajax_nopriv_get_tournament_standings', array( $this, 'ajax_get_tournament_standings' ) );
		add_action( 'wp_ajax_get_tournament_standings', array( $this, 'ajax_get_tournament_standings' ) );
	}

	public function activate() {
		update_option( 'league', array() );
		$this->update_database();
	}

	public function update_database() {
		$this->leagues->create_table();
		$this->tournaments->create_table();
		$this->players->create_table();
		$this->matches->create_table();
		$this->events->create_table();
	}

	public function get_ajaxurl() {
		?>
		<script type="text/javascript">
			var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
		</script>
	<?php
	}

	public function ajax_get_tournament_standings() {
		if ( isset( $_GET['tournament'] ) && is_numeric( $_GET['tournament'] ) ) {
			$result = $this->tournament_service->get_by_id( $_GET['tournament'] );

			header( 'Content-Type: text/html' );
			printf( '<h3>%s</h3>',
				__( sprintf( 'Standings from the %s tournament on %s', $result->get_format(),
					date_i18n( get_option( 'date_format' ), strtotime( $result->get_date() ) ) ), 'league' )
			);
			printf( '<table>' );
			printf( '<thead><tr><th>%s</th><th>%s</th><th>%s</th></tr>',
				__( 'Rank', 'league' ), __( 'Player', 'league' ), __( 'Points', 'rank' ) );
			foreach ( $result->get_standings() as $rank => $standing ) {
				printf( '<tr>' );
				printf( '<td>%s</td><td>%s</td><td>%s</td>',
					$rank, $this->players->get_by_id( $standing['player'] )->get_full_name(), $standing['points'] );
				printf( '</tr>' );
			}
			printf( '</table>' );
		} else {
			http_response_code( 406 );
			echo "No tournament id sent or it is non numeric.";
		}
		die();
	}
}