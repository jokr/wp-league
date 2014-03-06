<?php

class League_Service
{
    private $leagues;
	private $tournaments;

    public function __construct( Leagues $leagues, Tournaments $tournaments ) {
        $this->leagues = $leagues;
		$this->tournaments = $tournaments;
    }

    public function add_league( $name, $start, $end ) {
        $league = new League( $name, $start, $end );
        $this->leagues->save( $league );
    }

    public function get_by_id( $id ) {
        $array = $this->leagues->get_by_id( $id );
		$result = new League( $array['name'], $array['start'], $array['end'], unserialize($array['standings']) );
		$result->set_id( $id );
		$result->set_tournaments( $this->tournaments->get_by_league( $id ) );
		return $result;
    }

    public function get_all() {
		return $this->leagues->get_all();
    }

    public function delete( $id ) {
        $this->leagues->delete( $id );
    }

	public function exists( $id ) {
		return $this->leagues->exists($id);
	}
}