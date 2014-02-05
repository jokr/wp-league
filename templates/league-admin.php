<?php
if ( ! current_user_can( 'publish_pages' ) ) {
	wp_die( 'You do not have sufficient permissions to access this page.' );
}

require_once LEAGUE_PLUGIN_DIR . '/includes/view/class-leagues-list-table.php';

global $league_plugin;

$list_table = new Leagues_List_Table($league_plugin->get_leagues());
$list_table->prepare_items();

wp_enqueue_script( 'league-admin' );
wp_enqueue_style( 'league-admin' );

?>
<div class="wrap nosubsub">
	<h2><?php _e( 'Leagues', 'league' ) ?></h2>

	<div id="col-container">
		<div id="col-right">
			<div class="col-wrap">
				<?php $list_table->display(); ?>
			</div>
		</div>
		<!-- col right -->
		<div id="col-left">
			<div class="col-wrap">
				<div class="form-wrap">
					<h3><?php _e( 'Add new leagues', 'league' ) ?></h3>

					<form id="add-league" method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>"
					      class="validate">
						<input type="hidden" name="action" value="add_league"/>
						<?php wp_nonce_field( 'add-league', '_wpnonce_add_league' ); ?>

						<div class="form-field form-required">
							<label for="league-name"><?php _e( 'Name', 'league' ); ?></label>
							<input name="league[name]" id="league-name" type="text" value="" size="40"
							       required="true"/>

							<p><?php _e( 'The name is how it appears on your site.', 'league' ); ?></p>
						</div>

						<div class="form-field form-required">
							<div class="form-daterange daterange-from">
								<label for="league-start"><?php _e( 'Start Date', 'league' ); ?></label>
								<input name="league[start]" id="league-start" class="datepicker" type="text"
								       size="40" required="true"/>
							</div>
							<div class="form-daterange daterange-to">
								<label for="league-end"><?php _e( 'End Date', 'league' ); ?></label>
								<input name="league[end]" id="league-end" class="datepicker" type="text"
								       size="40" required="true"/>
							</div>
							<p><?php _e( 'The dates on which the league appears on the site.' ); ?></p>
						</div>
						<?php submit_button( __( 'Add New League', 'league' ) ); ?>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>