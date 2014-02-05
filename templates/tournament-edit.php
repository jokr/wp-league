<?php
if (! defined( 'ABSPATH' ))
    die( '-1' );

if (! current_user_can( 'publish_pages' )) {
    wp_die( 'You do not have sufficient permissions to access this page.' );
}

global $league_plugin;

$tournament = $league_plugin->get_tournaments()->get_by_id( (int)$_REQUEST['id'] );

if (is_wp_error( $tournament )) :
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

wp_enqueue_script( 'tournament-admin' );
wp_enqueue_style( 'tournament-admin' );

?>

<div class="wrap">
    <h2><?php _e( 'Edit Tournament', 'league' ) ?></h2>

    <form name="edit-item"
          id="edit-tournament" method="post"
          action="<?php echo admin_url( 'admin-post.php' ) ?>" class="validate">
        <input type="hidden" name="action" value="edit_tournament"/>
        <input type="hidden" name="id" value="<?php echo esc_attr( $tournament->getId() ); ?>"/>
        <input type="hidden" name="item" value="tournament"/>
        <?php wp_nonce_field( 'edit-tournament', '_wpnonce_edit_tournament' ); ?>
        <table class="form-table">
            <tr class="form-field form-required">
                <th scope="row"><label for="tournament-league"><?php _e( 'League', 'league' ); ?></label></th>
                <td><select name="tournament[league_id]" id="tournament-league" required="required">
                        <?php foreach ($leagues as $league) : ?>
                            <option
                                value="<?php echo $league->getId(); ?>"
                                <?php selected( $league->getId(), $tournament->getLeagueId() ) ?>>
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
                                                      value="<?php echo $date ?>"
                                                      size="40" required="required"/></div>
                    <div class="datetime time"><input id="tournament-time" class="timepicker" type="text"
                                                      value="<?php echo $time ?>"
                                                      size="10" required="required"/></div>

                    <p class="description"><?php _e( 'The date and time of the tournament.', 'league' ); ?></p>
                </td>
            </tr>
            <tr class="form-field form-required">
                <th scope="row"><label for="tournament-format"><?php _e( 'Format', 'league' ); ?></label></th>
                <td><input name="tournament[format]" id="tournament-format" type="text"
                           value="<?php echo $tournament->getFormat(); ?>"
                           size="40" required="required"/></td>
            </tr>
            <tr class="form-field">
                <th scope="row"><label for="tournament-url"><?php _e( 'Url', 'league' ); ?></label></th>
                <td><input name="tournament[url]" id="tournament-url" type="url"
                           value="<?php echo $tournament->getUrl(); ?>"
                           size="40"/></td>
            </tr>
        </table>
        <?php submit_button( __( 'Update' ) ); ?>
    </form>
    <?php if ('WAITING' === $tournament->getStatus()) : ?>
        <h2><?php _e( 'Upload Results', 'league' ) ?></h2>
        <form name="upload-results" id="upload-results" method="post" enctype="multipart/form-data"
              action="<?php echo admin_url( 'admin-post.php' ) ?>" class="validate">
            <input type="hidden" name="MAX_FILE_SIZE" value="30720"/>
            <input type="hidden" name="action" value="upload_results"/>
            <input type="hidden" name="id" value="<?php echo esc_attr( $tournament->getId() ); ?>"/>
            <?php wp_nonce_field( 'upload-results', '_wpnonce_upload_results' ); ?>

            <label for="results-file"><?php _e( 'Results File (.xml, max 30 KB)', 'league' ); ?></label><br>
            <input type="file" name="results-file" id="results-file"/>
            <?php submit_button( __( 'Upload' ) ); ?>
        </form>
    <?php elseif ('CLOSED' === $tournament->getStatus()) : ?>
        <h2><?php _e( 'View Results', 'league' ) ?></h2>
        <?php
        require_once LEAGUE_PLUGIN_DIR . 'includes/view/class-match-list-table.php';

        $list_table = new Match_List_Table( $tournament->getMatches(), $league_plugin->get_players() );

        $list_table->prepare_items();
        ?>
        <div id="col-container">
            <div id="col-right">
                <div class="col-wrap">
                    <?php $list_table->display(); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>