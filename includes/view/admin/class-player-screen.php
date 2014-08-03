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

		add_action( 'admin_post_add_points', array( $this, 'add_points' ) );
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

		wp_enqueue_script( 'player-admin' );
		wp_enqueue_style( 'player-admin' );

		switch ( $this->current_action() ) {
			case 'rewind':
				check_admin_referer( 'rewind-event', '_wpnonce' );
				$this->events->rewind( $_POST['event'] );
				wp_redirect( admin_url( 'admin.php?' . http_build_query( array(
						'page' => 'players',
						'deleted' => 'true',
						'action' => 'display',
						'id' => $_POST['player_id']
					) )
				) );
				break;
			case 'display':
				require_once LEAGUE_PLUGIN_DIR . 'includes/view/admin/class-events-list-table.php';
				wp_enqueue_style( 'player-admin' );
				$player = $this->players->get_by_id( $_GET['id'] );
				$events = new Events_List_Table( $player, $this->events );
				$events->prepare_items();
				?>
				<div class="wrap nosubsub">

					<h2><?php echo $player->get_full_name(); ?></h2>

					<h3><?php echo $player->get_credits(); ?> Credit Points</h3>
					<?php $this->player_admin( $player ); ?>
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

	public function add_points() {
		if ( ! current_user_can( 'publish_pages' ) ) {
			wp_die( 'You do not have sufficient permissions to access this page.' );
		}

		check_admin_referer( 'add-points', '_wpnonce_add_points' );
		if ( isset( $_POST['player_id'] ) && is_numeric( $_POST['player_id'] ) && $this->players->exists( $_POST['player_id'] ) ) {
			$this->players->add_credit_points( $_POST['player_id'], $_POST['amount'], $_POST['message'] );
			wp_redirect( admin_url( 'admin.php?' . http_build_query( array(
					'page' => 'players',
					'updated' => 'true',
					'action' => 'display',
					'id' => $_POST['player_id']
				) )
			) );
		} else {
			wp_redirect( add_query_arg( 'updated', 'false', admin_url( 'admin.php?page=players' ) ) );
		}
	}

	private function player_admin( Player $player ) {
		if ( current_user_can( 'publish_pages' ) ) :
			?>
			<div>
				<input type="button" id="add-points" class="button button-primary"
					   value="<?php _e( 'Add Points', 'league' ); ?>"/>
				<dialog id="add-points-window">
					<div>
						<h3><?php _e( 'Add Points', 'league' ); ?></h3>

						<form name="add-points" method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">
							<input type="hidden" name="action" value="add_points"/>
							<input type="hidden" name="player_id" value="<?php echo esc_attr( $player->get_id() ); ?>"/>
							<?php wp_nonce_field( 'add-points', '_wpnonce_add_points', true ); ?>
							<div class="dialog-field">
								<label>
									<?php _e( 'Amount', 'league' ); ?>
									<input name="amount" type="number" required="true" class="small"/>
								</label>
							</div>
							<div class="dialog-field">
								<label>
									<?php _e( 'Message', 'league' ); ?>
									<input name="message" type="text" required="true" class="wide"/>
								</label>
							</div>
							<div class="buttons">
								<input type="submit" class="button button-primary" id="add-points-confirm"
									   value="<?php _e( 'Add points', 'league' ); ?>"/>
								<input type="button" class="button button-secondary" id="add-points-cancel"
									   value="<?php _e( 'Cancel', 'league' ); ?>"/>
							</div>
						</form>
					</div>
				</dialog>
			</div>
		<?php endif;
	}
}