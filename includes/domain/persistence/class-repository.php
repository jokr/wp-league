<?php

abstract class Repository
{
	protected $table;
	protected $columns;
	protected $sort;

	public function __construct() {
		$this->sort = 'id';
		$this->columns = '*';
	}

	public function save( Model $element ) {
		global $wpdb;

		if ( null === $element->get_id() ) {
			$wpdb->insert( $this->table, $element->get_vars() );
			$element->set_id( $wpdb->insert_id );
		} else {
			$wpdb->update( $this->table, $element->get_vars(), array('id' => $element->get_id()) );
		}
		return $element->get_id();
	}

	protected function _get_by_id( $id ) {
		global $wpdb;
		return $wpdb->get_row( "SELECT $this->columns FROM $this->table WHERE id = $id", ARRAY_A );
	}

	public abstract function get_by_id( $id );

	protected function _get_all() {
		global $wpdb;
		return $wpdb->get_results( "SELECT $this->columns FROM $this->table ORDER BY $this->sort", ARRAY_A );
	}

	public abstract function get_all();

	protected function _query( $query ) {
		global $wpdb;
		return $wpdb->get_results( "SELECT $this->columns FROM $this->table " . $query, ARRAY_A );
	}

	public function exists( $id ) {
		global $wpdb;
		$result = $wpdb->get_row( "SELECT id FROM $this->table WHERE id = $id" );
		return ! empty($result);
	}

	public abstract function create_table();
}