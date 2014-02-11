<?php

require_once LEAGUE_PLUGIN_DIR . 'includes/view/class-list-table.php';

class Events_List_Table extends List_Table
{
    private $player;

    public function __construct( Player $player ) {
        $this->player = $player;
    }

    protected function get_items() {
        return League_Plugin::get_instance()->get_events()->get_by_player( $this->player );
    }

    protected function get_all_columns() {
        return array(
            'id' => 'ID',
            'date' => __( 'Date', 'league' ),
            'message' => __( 'Message', 'league' ),
            'type' => __( 'Type', 'league' )
        );
    }

    protected function get_grouped_columns() {
        return array( 'date' );
    }

    protected function column_date( $event ) {
        return date_i18n( get_option( 'date_format' ), strtotime( $event['date'] ) );
    }
}