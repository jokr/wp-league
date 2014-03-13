<?php
if ( ! defined( 'ABSPATH' ) )
	die( '-1' );

if ( ! current_user_can( 'publish_pages' ) ) {
	wp_die( 'You do not have sufficient permissions to access this page.' );
}

if ( 'OPEN' === $tournament->get_status() ) : ?>
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

	$matches = new Match_List_Table( $tournament );
	$standings = new Standings_List_Table( $tournament, new League_Rules( $tournament->get_standings() ) );

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