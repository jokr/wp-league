<?php

require_once LEAGUE_PLUGIN_DIR . 'includes/view/class-list-table.php';

class Leagues_List_Table extends List_Table
{
	private $leagues;

	public function __construct( Leagues $leagues ) {
		$this->leagues = $leagues;
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

	function column_start( League $league ) {
		return date_i18n( get_option( 'date_format' ), strtotime( $league->getStart() ) );
	}

	function column_end( League $league ) {
		return date_i18n( get_option( 'date_format' ), strtotime( $league->getEnd() ) );
	}

	function column_tournaments( League $league ) {
		return count( $league->getTournaments() );
	}
}
