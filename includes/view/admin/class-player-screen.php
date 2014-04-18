<?php

require_once dirname( __FILE__ ) . '/class-admin-screen.php';

class Player_Screen extends Admin_Screen
{
	private static $instance;

	private $players;
	private $events;

	public static function get_instance( Player_Service $players = null, Event_Service $events = null ) {
		if ( null == self::$instance ) {
			self::$instance = new Player_Screen( $players, $events );
		}
		return self::$instance;
	}

	protected function __construct( Player_Service $players, Event_Service $events ) {
		parent::__construct();
		$this->players = $players;
		$this->events = $events;
	}

	public function add_admin_menu() {
		add_submenu_page(
			'leagues',
			'Players',
			'Players',
			'publish_pages',
			'players',
			array( $this, 'load_players_menu' )
		);
	}

	public function load_players_menu() {
		if ( ! current_user_can( 'publish_pages' ) ) {
			wp_die( 'You do not have sufficient permissions to access this page.' );
		}

		switch ( $this->current_action() ) {
			case 'display':
				require_once LEAGUE_PLUGIN_DIR . 'includes/view/admin/class-events-list-table.php';
				wp_enqueue_style( 'player-admin' );
				$player = $this->players->get_by_id( $_GET['id'] );
				$events = new Events_List_Table( $player, $this->events );
				$events->prepare_items();
				?>
				<div class="wrap nosubsub">

					<h2><?php echo $player->get_full_name() ?></h2>

					<div id="col-container">
						<div class="col-wrap">
							<?php $events->display(); ?>
							<br class="clear"/>
						</div>
					</div>
				</div>
				<?php
				break;
			default:
				load_template( LEAGUE_PLUGIN_DIR . 'templates/player-admin.php' );
		}
	}

	public function get_players() {
		return $this->players;
	}

	public function get_events() {
		return $this->events;
	}
}