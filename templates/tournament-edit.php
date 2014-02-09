<?php
if ( ! defined( 'ABSPATH' ) )
	die('-1');

if ( ! current_user_can( 'publish_pages' ) ) {
	wp_die( 'You do not have sufficient permissions to access this page.' );
}

wp_enqueue_script( 'tournament-admin' );
wp_enqueue_style( 'tournament-admin' );

global $league_plugin;

$tournament = $league_plugin->get_tournaments()->get_by_id( (int) $_REQUEST['id'] );

if ( is_wp_error( $tournament ) ) :
	?>
	<div id="message" class="error">
		<p>
			<strong><?php _e( 'You did not select a valid item id for editing.', 'league' ); ?></strong>
		</p>
	</div>
	<?php
	return;
endif;

$leagues = $league_plugin->get_leagues()->get_all();

$disabled = in_array( $tournament->getStatus(), array('FINISHED', 'CLOSED') );

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
								<?php selected( $league->get_id(), $tournament->getLeagueId() ) ?>>
								<?php echo $league->getName(); ?></option>
						<?php endforeach; ?>
					</select>

					<p class="description"><?php _e( 'The league the tournament belongs to.', 'league' ); ?></p>
				</td>
			</tr>
			<tr class="form-field form-required">
				<input id="tournament-date-field" type="hidden" name="tournament[date]"
				       value="<?php echo $tournament->getDate() ?>"/>
				<th scope="row"><label for="tournament-date"><?php _e( 'Date and Time', 'league' ); ?></label></th>
				<td>
					<?php
					$date = date( 'Y-m-d', strtotime( $tournament->getDate() ) );
					$time = date( 'H:i', strtotime( $tournament->getDate() ) );
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
				           value="<?php echo $tournament->getFormat(); ?>"
				           size="40" required="required" <?php disabled( $disabled ) ?>/></td>
			</tr>
			<tr class="form-field">
				<th scope="row"><label for="tournament-url"><?php _e( 'Url', 'league' ); ?></label></th>
				<td><input name="tournament[url]" id="tournament-url" type="url"
				           value="<?php echo $tournament->getUrl(); ?>"
				           size="40" <?php disabled( $disabled ) ?>/></td>
			</tr>
		</table>
		<?php if ( ! $disabled ) submit_button( __( 'Update' ) ); ?>
	</form>
	<?php include_once dirname( __FILE__ ) . '/tournament-results.php' ?>
</div>