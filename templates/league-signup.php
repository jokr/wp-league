<?php

wp_enqueue_script( 'league-signup', LEAGUE_PLUGIN_URL . 'css/signup.css', array(), LEAGUE_PLUGIN_VERSION );

/** Sets up the WordPress Environment. */
add_action( 'wp_head', 'wp_no_robots' );

get_header();

?>
    <div id="content" class="widecolumn">
        <form id="league-register">
            <label for="user_name"><?php _e( 'Name', 'league' ) ?></label>
            <input name="user_name" type="text" id="user_name"
                   maxlength="200" required="required"/><br/>
            <label for="user_email"><?php _e( 'Email Address', 'league' ) ?></label>
            <input name="user_email" type="email" id="user_email"
                   maxlength="200" required="required"/><br/>
            <label for="user_email"><?php _e( 'Repeat Email Address', 'league' ) ?></label>
            <input name="user_email_repeat" type="email" id="user_email_repeat"
                   maxlength="200" required="required"/><br/>
            <input name="submit" type="submit" value="<?php _e( 'Register', 'league' ); ?>">
        </form>
    </div>
<?php

get_footer();
