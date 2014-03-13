<?php

wp_enqueue_style( 'league-signup', LEAGUE_PLUGIN_URL . 'css/signup.css', array(), LEAGUE_PLUGIN_VERSION );
wp_enqueue_script( 'league-signup', LEAGUE_PLUGIN_URL . 'js/signup.js', array( 'jquery' ), LEAGUE_PLUGIN_VERSION, true );

/** Sets up the WordPress Environment. */
add_action( 'wp_head', 'wp_no_robots' );

get_header();

?>
	<div id="content" class="widecolumn">
		<div id="messages"></div>
		<form id="league-register">
			<label for="user_first"><?php _e( 'First Name', 'league' ) ?></label>
			<input name="user_first" type="text" id="user_first"
				   maxlength="200" required="required"/><br/>
			<label for="user_last"><?php _e( 'Last Name', 'league' ) ?></label>
			<input name="user_last" type="text" id="user_last"
				   maxlength="200" required="required"/><br/>

			<p><?php _e( 'Please use your real name and not a nick name.', 'league' ); ?></p>
			<label for="user_email"><?php _e( 'Email Address', 'league' ) ?></label>
			<input name="user_email" type="email" id="user_email"
				   maxlength="200" required="required"/><br/>
			<label for="user_email_repeat"><?php _e( 'Repeat Email Address', 'league' ) ?></label>
			<input name="user_email_repeat" type="email" id="user_email_repeat"
				   maxlength="200" required="required"/><br/>

			<p><?php _e( 'We will use this email address to send your password to you, so make sure it is correct.', 'league' ); ?></p>
			<label for="user_dci"><?php _e( 'DCI Number', 'league' ) ?></label>
			<input name="user_dci" type="text" id="user_dci"
				   maxlength="200" required="required"/><br/>

			<p><?php _e( 'You should have a dci number if you played in one of our tournaments. If you do not know your number, please contact us.', 'league' ); ?></p>
			<input name="submit" id="league-signup" type="submit" value="<?php _e( 'Register', 'league' ); ?>">
		</form>
	</div>
<?php

get_footer();
