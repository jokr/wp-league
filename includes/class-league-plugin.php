<?php

require_once dirname( __FILE__ ) . '/domain/persistence/class-leagues.php';
require_once dirname( __FILE__ ) . '/domain/persistence/class-tournaments.php';
require_once dirname( __FILE__ ) . '/domain/persistence/class-players.php';
require_once dirname( __FILE__ ) . '/domain/persistence/class-matches.php';
include_once dirname( __FILE__ ) . '/domain/persistence/class-league-events.php';
require_once dirname( __FILE__ ) . '/view/frontend/class-league-shortcode.php';
require_once dirname( __FILE__ ) . '/view/admin/class-league-screen.php';

require_once dirname( __FILE__ ) . '/class-league-signup-page.php';

include_once dirname( __FILE__ ) . '/class-wer-result-handler.php';

class League_Plugin
{
    const VERSION = '0.0.1';

    protected $plugin_slug;

    private $leagues;
    private $tournaments;
    private $players;
    private $matches;
    private $events;

    private static $instance;

    public static function get_instance() {
        if (null == self::$instance) {
            self::$instance = new League_Plugin();
        }

        return self::$instance;
    }

    private function __construct() {
        // Setup repositories
        $this->matches = new Matches();
        $this->tournaments = new Tournaments( $this->matches );
        $this->leagues = new Leagues( $this->tournaments );
        $this->players = new Players();
        $this->events = new League_Events();

        // Register activition and deactiviation
        register_activation_hook( __FILE__, array( $this, 'activate' ) );

        if (is_admin()) {
            new League_Screen( $this );
        } else {
            add_action( 'wp_head', array( $this, 'get_ajaxurl' ) );
            new League_Signup_Page( array(
                'url'		=> 'league-signup',
                'pagename'	=> 'league-signup'
            ));
        }

        add_action( 'init', array( $this, 'init' ) );
    }

    public function init() {
        add_shortcode( 'league', array( 'League_Shortcode', 'render' ) );
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
        if (isset( $_GET['tournament'] ) && is_numeric( $_GET['tournament'] )) {
            $result = $this->tournaments->get_by_id( $_GET['tournament'] );

            header( 'Content-Type: text/html' );
            printf( '<h3>%s</h3>',
                __( sprintf( 'Standings from the %s tournament on %s', $result->getFormat(),
                    date_i18n( get_option( 'date_format' ), strtotime( $result->getDate() ) ) ), 'league' )
            );
            printf( '<table>' );
            printf( '<thead><tr><th>%s</th><th>%s</th><th>%s</th></tr>',
                __( 'Rank', 'league' ), __( 'Player', 'league' ), __( 'Points', 'rank' ) );
            foreach ($result->get_standings() as $rank => $standing) {
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

    public function get_leagues() {
        return $this->leagues;
    }

    public function get_tournaments() {
        return $this->tournaments;
    }

    public function get_players() {
        return $this->players;
    }

    public function get_matches() {
        return $this->matches;
    }

    public function get_events() {
        return $this->events;
    }
}