<?php

require_once LEAGUE_PLUGIN_DIR . 'includes/view/class-list-table.php';

class Events_List_Table extends List_Table
{
	private $player;
	private $events;

	public function __construct( Player $player, Event_Service $events ) {
		$this->player = $player;
		$this->events = $events;
	}

	protected function get_items() {
		return $this->events->get_by_player( $this->player->get_id() );
	}

	protected function get_all_columns() {
		return array(
			'id' => 'ID',
			'date' => __( 'Date', 'league' ),
			'message' => __( 'Message', 'league' ),
			'type' => __( 'Type', 'league' )
		);
	}

	protected function column_message( League_Event $event ) {
		return $event->get_message() . sprintf( '<div class="row-actions"><span class="delete"><a href="%1$s">%2$s</a></span></div>',
			admin_url( 'admin.php?' . http_build_query( array(
					'page' => 'players',
					'action' => 'rewind',
					'id' => $event->get_player()->get_id(),
					'event' => $event->get_id(),
					'_wpnonce' => wp_create_nonce( 'rewind-event' )
				) )
			),
			__( 'Rewind' )
		);
	}

	protected function get_grouped_columns() {
		return array( 'date' );
	}

	protected function column_date( League_Event $event ) {
		return date_i18n( get_option( 'date_format' ), strtotime( $event->get_date() ) );
	}
}