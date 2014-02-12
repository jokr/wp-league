<?php

require_once LEAGUE_PLUGIN_DIR . 'includes/view/class-list-table.php';

class Leagues_List_Table extends List_Table
{
    private $leagues;
    private $tournaments;

    public function __construct( League_Service $leagues, Tournament_Service $tournaments ) {
        $this->leagues = $leagues;
        $this->tournaments = $tournaments;
    }

    protected function get_items() {
        return $this->leagues->get_all();
    }

    function get_sortable_columns() {
        return array(
            'name' => 'name',
            'start' => 'start',
            'end' => 'end',
            'tournaments' => 'tournaments'
        );
    }

    protected function get_all_columns() {
        return array(
            'id' => 'ID',
            'name' => __( 'Name', 'league' ),
            'start' => __( 'Start Date', 'league' ),
            'end' => __( 'End Date', 'league' ),
            'tournaments' => __( 'Tournaments', 'league' )
        );
    }

    function column_start( $league ) {
        return date_i18n( get_option( 'date_format' ), strtotime( $league['start'] ) );
    }

    function column_end( $league ) {
        return date_i18n( get_option( 'date_format' ), strtotime( $league['end'] ) );
    }

    function column_tournaments( $league ) {
        return count( $this->tournaments->get_all_for_league( $league['id'] ) );
    }
}
