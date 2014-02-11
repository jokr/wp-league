<?php

include_once LEAGUE_PLUGIN_DIR . 'includes/view/class-list-table.php';

class League_Schedule_List extends List_Table
{
    private $league;
    private $players;

    public function __construct( League $league, Players $players ) {
        $this->league = $league;
        $this->players = $players;
    }

    protected function get_items() {
        return $this->league->get_tournaments();
    }

    protected function get_all_columns() {
        return array(
            'id' => 'ID',
            'date' => __( 'Date', 'league' ),
            'format' => __( 'Format', 'league' ),
            'winner' => __( 'Winner', 'league' ),
            'players' => __( 'Players', 'players' )
        );
    }

    protected function sort() {
        uasort( $this->items, function ( Tournament $a, Tournament $b ) {
            return strtotime( $a->getDate() ) - strtotime( $b->getDate() );
        } );
    }

    protected function get_table_classes() {
        return array( 'league', 'league-schedule' );
    }

    protected function get_row_classes( Tournament $tournament ) {
        return array( 'tournament-' . strtolower( $tournament->get_status() ) );
    }

    protected function column_date( Tournament $tournament ) {
        $date = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $tournament->getDate() ) );
        if ( 'CLOSED' == $tournament->get_status() ) {
            return esc_html( $date );
        } elseif ( strlen( $tournament->getUrl() ) > 0 ) {
            return sprintf( '<a href="%s">%s</a>', esc_url( $tournament->getUrl() ), esc_html( $date ) );
        } else {
            return esc_html( $date );
        }
    }

    protected function column_winner( Tournament $tournament ) {
        if ( 'CLOSED' == $tournament->get_status() ) {
            return esc_html( $this->players->get_by_id( $tournament->get_winner() )->get_full_name() );
        } else {
            return '';
        }
    }

    protected function column_players( Tournament $tournament ) {
        if ( 'CLOSED' == $tournament->get_status() ) {
            return count( $tournament->get_standings() );
        } else {
            return '';
        }
    }
}