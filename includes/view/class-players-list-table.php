<?php

require_once dirname( __FILE__ ) . '/class-list-table.php';

class Players_List_Table extends List_Table
{
	private $players;

	public function __construct( Players $players ) {
		$this->players = $players;
	}

	protected function get_items() {
		return $this->players->get_all();
	}

	protected function get_all_columns() {
		return array(
			'id' => 'ID',
			'name' => __( 'Name', 'league' ),
			'dci' => __( 'DCI', 'league' ),
			'credits' => __( 'Credits', 'league' )
		);
	}

	protected function column_name( Player $player ) {
		return $player->getFirst() . ' ' . $player->getLast();
	}
}