<?php

class League_Service
{
    private $leagues;

    public function __construct( Leagues $leagues ) {
        $this->leagues = $leagues;
    }

    public function add_league( $name, $start, $end ) {
        $league = new League( $name, $start, $end );
        $this->leagues->save( $league );
    }

    public function get_by_id( $id ) {
        return $this->leagues->get_by_id( $id );
    }

    public function get_all() {
        return $this->leagues->get_all();
    }

    public function delete( $id ) {
        $this->leagues->delete( $id );
    }
}