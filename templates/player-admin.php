<?php
if (! current_user_can( 'publish_pages' )) {
    wp_die( 'You do not have sufficient permissions to access this page.' );
}

include_once LEAGUE_PLUGIN_DIR . '/includes/view/class-players-list-table.php';

global $league_plugin;

$list_table = new Players_List_Table( $league_plugin->get_players() );
$list_table->prepare_items();

wp_enqueue_script( 'tournament-admin' );
wp_enqueue_style( 'tournament-admin' );
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