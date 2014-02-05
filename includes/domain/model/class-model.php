<?php

abstract class Model
{
	protected $id;
	protected $created;

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

	/**
	 * @param mixed $id
	 */
	public function setId( $id ) {
		$this->id = $id;
	}

	/**
	 * @return mixed
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param mixed $created
	 */
	protected function setCreated( $created ) {
		$this->created = $created;
	}

	/**
	 * @return mixed
	 */
	public function getCreated() {
		return $this->created;
	}
}