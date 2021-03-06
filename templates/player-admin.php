<?php
if ( ! current_user_can( 'publish_pages' ) ) {
	wp_die( 'You do not have sufficient permissions to access this page.' );
}

include_once LEAGUE_PLUGIN_DIR . 'includes/view/admin/class-players-list-table.php';

$screen = Player_Screen::get_instance();

$list_table = new Players_List_Table( $screen->get_players() );
$list_table->prepare_items();

?>

<div class="wrap nosubsub">

	<h2><?php _e( 'Players', 'league' ) ?></h2>

	<div id="col-container">
		<div class="col-wrap">
			<?php $list_table->display(); ?>
			<br class="clear"/>
		</div>
	</div>
</div>