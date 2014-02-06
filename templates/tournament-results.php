<?php
if ( ! defined( 'ABSPATH' ) )
	die('-1');

if ( ! current_user_can( 'publish_pages' ) ) {
	wp_die( 'You do not have sufficient permissions to access this page.' );
}

if ( 'WAITING' === $tournament->getStatus() ) : ?>
	<h2><?php _e( 'Upload Results', 'league' ) ?></h2>
	<form name="upload-results" id="upload-results" method="post" enctype="multipart/form-data"
	      action="<?php echo admin_url( 'admin-post.php' ) ?>" class="validate">
		<input type="hidden" name="MAX_FILE_SIZE" value="30720"/>
		<input type="hidden" name="action" value="upload_results"/>
		<input type="hidden" name="id" value="<?php echo esc_attr( $tournament->getId() ); ?>"/>
		<?php wp_nonce_field( 'upload-results', '_wpnonce_upload_results' ); ?>

		<label for="results-file"><?php _e( 'Results File (.xml, max 30 KB)', 'league' ); ?></label><br>
		<input type="file" name="results-file" id="results-file"/>
		<?php submit_button( __( 'Upload Results', 'league' ) ); ?>
	</form>
<?php elseif ( in_array( $tournament->getStatus(), array('FINISHED', 'CLOSED') ) ) : ?>
	<h2><?php _e( 'View Results', 'league' ) ?></h2>
	<div id="result-controls">
		<input type="button" class="button button-red" id="delete-results"
		       value="<?php _e( 'Delete Results', 'league' ); ?>"/>
		<dialog id="delete-confirm-window">
			<div>
				<h3><?php _e( 'Are you sure?', 'league' ); ?></h3>

				<p><?php _e( 'Deleting the results will rewind all points gained from this tournament and delete all matches.',
						'league' ); ?></p>

				<form name="delete-results" method="post" action="<?php echo admin_url( 'admin-post.php' ) ?>">
					<input type="hidden" name="action" value="delete_results"/>
					<input type="hidden" name="id" value="<?php echo esc_attr( $tournament->getId() ); ?>"/>
					<?php wp_nonce_field( 'delete-results', '_wpnonce_delete_results' ); ?>
					<input type="submit" class="button button-primary" id="delete-confirm"
					       value="<?php _e( 'Yes, I am sure.', 'league' ); ?>"/>
					<input type="button" class="button button-secondary" id="delete-cancel"
					       value="<?php _e( 'No, cancel.', 'league' ); ?>"/>
				</form>
			</div>
		</dialog>
	</div>
	<?php
	require_once LEAGUE_PLUGIN_DIR . 'includes/view/class-match-list-table.php';
	require_once LEAGUE_PLUGIN_DIR . 'includes/view/class-standings-list-table.php';

	$matches = new Match_List_Table($tournament->getMatches());
	$standings = new Standings_List_Table($tournament);

	$matches->prepare_items();
	$standings->prepare_items();
	?>
	<div id="col-container">
		<div id="col-right">
			<div class="col-wrap">
				<?php $standings->display(); ?>
			</div>
		</div>
		<div id="col-left">
			<div class="col-wrap">
				<?php $matches->display(); ?>
			</div>
		</div>
	</div>
<?php endif; ?>