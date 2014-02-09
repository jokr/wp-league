<?php

abstract class Model
{
	protected $id;

	public function __construct( array $properties ) {
		foreach ( $properties as $key => $value ) {
			if ( property_exists( $this, $key ) ) {
				$this->$key = $value;
			}
		}
	}

	public function get_vars() {
		$result = get_object_vars( $this );
		foreach ( $result as $key => $value ) {
			if ( ! isset($value) ) {
				unset($result[$key]);
			}
		}
		return $result;
	}

	public abstract function save();

	public function set_id( $id ) {
		$this->id = $id;
	}

	public function get_id() {
		return $this->id;
	}
}