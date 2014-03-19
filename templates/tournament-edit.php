<?php
if ( ! defined( 'ABSPATH' ) )
	die( '-1' );

if ( ! current_user_can( 'publish_pages' ) ) {
	wp_die( 'You do not have sufficient permissions to access this page.' );
}

wp_enqueue_script( 'tournament-admin' );
wp_enqueue_style( 'tournament-admin' );

$screen = Tournament_Screen::get_instance();

$tournament = $screen->get_tournaments()->get_by_id( (int)$_REQUEST['id'] );

$leagues = $screen->get_leagues()->get_all();

$disabled = in_array( $tournament->get_status(), array( 'FINISHED', 'CLOSED' ) );

?>

<div class="wrap">
	<h2><?php _e( 'Edit Tournament', 'league' ) ?></h2>

	<form name="edit-item"
		  id="edit-tournament" method="post"
		  action="<?php echo admin_url( 'admin-post.php' ) ?>" class="validate">
		<input type="hidden" name="action" value="edit_tournament"/>
		<input type="hidden" name="id" value="<?php echo esc_attr( $tournament->get_id() ); ?>"/>
		<input type="hidden" name="item" value="tournament"/>
		<?php wp_nonce_field( 'edit-tournament', '_wpnonce_edit_tournament' ); ?>
		<table class="form-table">
			<tr class="form-field form-required">
				<th scope="row"><label for="tournament-league"><?php _e( 'League', 'league' ); ?></label></th>
				<td><select name="tournament[league_id]" id="tournament-league"
							required="required" <?php disabled( $disabled ) ?>>
						<?php foreach ( $leagues as $league ) : ?>
							<option
								value="<?php echo $league->get_id(); ?>"
								<?php selected( $league->get_id(), $tournament->get_league_id() ) ?>>
								<?php echo $league->get_name(); ?></option>
						<?php endforeach; ?>
					</select>

					<p class="description"><?php _e( 'The league the tournament belongs to.', 'league' ); ?></p>
				</td>
			</tr>
			<tr class="form-field form-required">
				<input id="tournament-date-field" type="hidden" name="tournament[date]"
					   value="<?php echo $tournament->get_date() ?>"/>
				<th scope="row"><label for="tournament-date"><?php _e( 'Date and Time', 'league' ); ?></label></th>
				<td>
					<?php
					$date = date( 'Y-m-d', strtotime( $tournament->get_date() ) );
					$time = date( 'H:i', strtotime( $tournament->get_date() ) );
					?>
					<div class="datetime date"><input id="tournament-date" class="datepicker" type="text"
													  value="<?php echo $date ?>" <?php disabled( $disabled ) ?>
													  size="40" required="required"/></div>
					<div class="datetime time"><input id="tournament-time" class="timepicker" type="text"
													  value="<?php echo $time ?>" <?php disabled( $disabled ) ?>
													  size="10" required="required"/></div>

					<p class="description"><?php _e( 'The date and time of the tournament.', 'league' ); ?></p>
				</td>
			</tr>
			<tr class="form-field form-required">
				<th scope="row"><label for="tournament-format"><?php _e( 'Format', 'league' ); ?></label></th>
				<td><input name="tournament[format]" id="tournament-format" type="text"
						   value="<?php echo $tournament->get_format(); ?>"
						   size="40" required="required" <?php disabled( $disabled ) ?>/></td>
			</tr>
			<tr class="form-field">
				<th scope="row"><label for="tournament-url"><?php _e( 'Url', 'league' ); ?></label></th>
				<td><input name="tournament[url]" id="tournament-url" type="url"
						   value="<?php echo $tournament->get_url(); ?>"
						   size="40" <?php disabled( $disabled ) ?>/></td>
			</tr>
		</table>
		<?php if ( ! $disabled ) submit_button( __( 'Update' ) ); ?>
	</form>
	<?php if ( 'OPEN' === $tournament->get_status() ) : ?>
		<h2><?php _e( 'Upload Results', 'league' ) ?></h2>
		<form name="upload-results" id="upload-results" method="post" enctype="multipart/form-data"
			  action="<?php echo admin_url( 'admin-post.php' ) ?>" class="validate">
			<input type="hidden" name="MAX_FILE_SIZE" value="30720"/>
			<input type="hidden" name="action" value="upload_results"/>
			<input type="hidden" name="id" value="<?php echo esc_attr( $tournament->get_id() ); ?>"/>
			<?php wp_nonce_field( 'upload-results', '_wpnonce_upload_results' ); ?>

			<label for="results-file"><?php _e( 'Results File (.xml, max 30 KB)', 'league' ); ?></label><br>
			<input type="file" name="results-file" id="results-file"/>
			<?php submit_button( __( 'Upload Results', 'league' ) ); ?>
		</form>
	<?php elseif ( in_array( $tournament->get_status(), array( 'FINISHED', 'CLOSED' ) ) ) : ?>
		<h2><?php _e( 'View Results', 'league' ) ?></h2>
		<?php
		require_once LEAGUE_PLUGIN_DIR . 'includes/view/admin/class-match-list-table.php';
		require_once LEAGUE_PLUGIN_DIR . 'includes/view/admin/class-standings-list-table.php';
		require_once LEAGUE_PLUGIN_DIR . 'includes/class-league-rules.php';

		$matches = new Match_List_Table( $tournament, $screen->get_players() );
		$standings = new Standings_List_Table( $tournament, new League_Rules( $tournament->get_standings() ), $screen->get_players() );

		$matches->prepare_items();
		$standings->prepare_items();
		?>
		<div id="col-container">
			<div id="col-right">
				<div class="col-wrap">
					<form id="save-points" name="save-points" method="post"
						  action="<?php echo admin_url( 'admin-post.php' ); ?>">
						<input type="hidden" name="action" value="save_points"/>
						<input type="hidden" name="id" value="<?php echo esc_attr( $tournament->get_id() ); ?>"/>
						<?php wp_nonce_field( 'save-points', '_wpnonce_save_points' ); ?>
						<?php $standings->display(); ?>
					</form>
				</div>
			</div>
			<div id="col-left">
				<div class="col-wrap">
					<?php $matches->display(); ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>