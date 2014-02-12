<?php

abstract class Model
{
    protected $id;

    public function get_vars() {
        $result = get_object_vars( $this );
        foreach ( $result as $key => $value ) {
            if ( ! isset( $value ) ) {
                unset( $result[$key] );
            }
            if ( is_array( $value ) ) {
                $result[$key] = serialize( $value );
            }
        }
        return $result;
    }

    public function set_id( $id ) {
        $this->id = $id;
    }

    public function get_id() {
        return $this->id;
    }
}