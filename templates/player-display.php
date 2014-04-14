<?php

if ( ! defined( 'ABSPATH' ) )
	die( '-1' );

if ( ! current_user_can( 'publish_pages' ) ) {
	wp_die( 'You do not have sufficient permissions to access this page.' );
}

$screen = Player_Screen::get_instance();
$player = $screen->get_players();

require_once LEAGUE_PLUGIN_DIR . 'includes/view/admin/class-events-list-table.php';

$events = new Events_List_Table( $player );
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

