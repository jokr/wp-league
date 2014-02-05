<?php

require_once dirname( __FILE__ ) . '/class-list-table.php';

class Tournaments_List_Table extends List_Table
{
	private $tournaments;
	private $leagues;

	public function __construct( Tournaments $tournaments, Leagues $leagues ) {
		$this->tournaments = $tournaments;
		$this->leagues = $leagues;
	}

	protected function get_items() {
		return $this->tournaments->get_all();
	}

	public function get_all_columns() {
		return array(
			'id' => 'ID',
			'tdate' => __( 'Date', 'league' ),
			'format' => __( 'Format', 'league' ),
			'league' => __( 'League', 'league' ),
			'status' => __( 'Status', 'league' ),
			'url' => __( 'Url', 'league' )
		);
	}

	public function get_column_widths() {
		return array(
			'tdate' => '15%',
			'format' => '15%',
			'status' => '15%',
			'url' => '40%'
		);
	}

	function column_tdate( Tournament $tournament ) {
		$query = array(
			'page' => 'tournaments',
			'action' => 'edit',
			'id' => $tournament->getId()
		);
		$edit_link = esc_url( admin_url( 'admin.php' ) . '?' . http_build_query( $query ) );
		$date = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $tournament->getDate() ) );
		return '<a href="' . $edit_link . '">' . $date . '</a>';
	}

	public function column_league( Tournament $tournament ) {
		return $this->leagues->get_by_id( $tournament->getLeagueId() )->getName();
	}

	public function column_url( Tournament $tournament ) {
		return '<a href="' . $tournament->getUrl() . '" target="_blank">' . $tournament->getUrl() . '</a>';
	}
}
