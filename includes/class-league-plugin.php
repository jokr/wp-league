<?php

require_once dirname( __FILE__ ) . '/domain/persistence/class-leagues.php';
require_once dirname( __FILE__ ) . '/domain/persistence/class-tournaments.php';
require_once dirname( __FILE__ ) . '/domain/persistence/class-players.php';
require_once dirname( __FILE__ ) . '/domain/persistence/class-matches.php';
require_once dirname( __FILE__ ) . '/view/class-league-shortcode.php';
require_once dirname( __FILE__ ) . '/view/admin-screen/class-league-screen.php';
include_once dirname( __FILE__ ) . '/class-wer-result-handler.php';

class League_Plugin
{
    private $leagues;
    private $tournaments;
    private $players;
    private $matches;

    public function __construct() {
        $this->matches = new Matches();
        $this->tournaments = new Tournaments($this->matches);
        $this->leagues = new Leagues($this->tournaments);
        $this->players = new Players();

	    new League_Screen($this);

        add_shortcode( 'league', array('League_Shortcode', 'render') );
    }

    public function activate() {
        update_option( 'league', array() );
        $this->update_database();
    }

    public function update_database() {
        $this->leagues->create_table();
	    $this->tournaments->create_table();
        $this->players->create_table();
	    $this->matches->create_table();
    }

    public function get_setting( $handle ) {
        $options = get_option( 'league' );
        if (isset($options[$handle])) {
            return $options[$handle];
        } else {
            return null;
        }
    }

    public function set_setting( $handle, $new_value ) {
        $options = get_option( 'league' );
        $options[$handle] = $new_value;
        update_option( 'league', $options );
    }

    public function get_leagues() {
        return $this->leagues;
    }

    public function get_tournaments() {
        return $this->tournaments;
    }

    public function get_players() {
        return $this->players;
    }

    public function get_matches() {
        return $this->matches;
    }
}