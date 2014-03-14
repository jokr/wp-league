<?php
if ( ! current_user_can( 'publish_pages' ) ) {
	wp_die( 'You do not have sufficient permissions to access this page.' );
}

include_once LEAGUE_PLUGIN_DIR . 'includes/view/admin/class-tournaments-list-table.php';

$leagues = League_Plugin::get_instance()->get_active_leagues();

?>
<div class="wrap nosubsub">

	<h2><?php _e( 'Tournaments', 'league' ) ?></h2>

	<div id="col-container">
		<div id="col-right">
			<div class="col-wrap">
				<?php $list_table->display(); ?>
				<br class="clear"/>
			</div>
		</div>
		<!-- col right -->
		<div id="col-left">
			<div class="col-wrap">
				<div class="form-wrap">
					<h3><?php _e( 'Add new tournaments', 'league' ) ?></h3>

					<form id="add-tournament" method="post"
						  action="<?php echo admin_url( 'admin-post.php' ) ?>"
						  class="validate">
						<input type="hidden" name="action" value="add_tournament"/>
						<?php wp_nonce_field( 'add-tournament', '_wpnonce_add_tournament' ); ?>

						<div class="form-field form-required">
							<label for="tournament-league"><?php _e( 'League', 'league' ); ?></label>
							<select name="tournament[league_id]" id="tournament-league" required="required">
								<?php foreach ( $leagues as $league ) : ?>
									<option
										value="<?php echo $league->get_id(); ?>"><?php echo $league->get_name(); ?></option>
								<?php endforeach; ?>
							</select>

							<p><?php _e( 'The league the tournament belongs to.', 'league' ); ?></p>
						</div>

						<div class="form-field form-required">
							<input id="tournament-date-field" type="hidden" name="tournament[date]"
								   value=""/>

							<div class="datetime date">
								<label for="tournament-date"><?php _e( 'Date', 'league' ); ?></label>
								<input id="tournament-date" class="datepicker" type="text"
									   size="40" required="required"/>
							</div>
							<div class="datetime time">
								<label for="tournament-time"><?php _e( 'Time', 'league' ); ?></label>
								<input id="tournament-time" class="timepicker" type="text"
									   size="10" required="required"/>
							</div>
							<p><?php _e( 'The date and time of the tournament.', 'league' ); ?></p>
						</div>
						<div class="form-field form-required">
							<label for="tournament-format"><?php _e( 'Format', 'league' ); ?></label>
							<input name="tournament[format]" id="tournament-format" type="text"
								   size="40" required="required"/>
						</div>

						<div class="form-field">
							<label for="tournament-url"><?php _e( 'Url', 'league' ); ?></label>
							<input name="tournament[url]" id="tournament-url" type="url"
								   size="40"/>
						</div>

						<?php submit_button( __( 'Add New Tournament', 'league' ) ); ?>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>