<?php

if ( !defined( 'ABSPATH' ) )
    die( '-1' );

if ( !current_user_can( 'publish_pages' ) ) {
    wp_die( 'You do not have sufficient permissions to access this page.' );
}

$player = League_Plugin::get_instance()->get_players()->get_by_id( $_REQUEST['id'] );

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

