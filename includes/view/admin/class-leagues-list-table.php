<?php

require_once LEAGUE_PLUGIN_DIR . 'includes/view/class-list-table.php';

class Leagues_List_Table extends List_Table
{
	private $screen;

	public function __construct( League_Screen $screen ) {
		$this->screen = $screen;
	}

	protected function get_items() {
		return $this->screen->get_all_leagues();
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

	function column_name( League $league ) {
		return $league->get_name() . parent::row_actions(
			$this->screen->get_edit_url( $league->get_id() ),
			$this->screen->get_delete_url( $league->get_id() )
		);
	}

	function column_start( League $league ) {
		return date_i18n( get_option( 'date_format' ), strtotime( $league->get_start() ) );
	}

	function column_end( League $league ) {
		return date_i18n( get_option( 'date_format' ), strtotime( $league->get_end() ) );
	}

	function column_tournaments( League $league ) {
		return $this->screen->get_tournament_count( $league->get_id() );
	}
}
