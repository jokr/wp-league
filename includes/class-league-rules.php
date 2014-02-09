<?php

class League_Rules
{
	private $standings;
	private $entry;
	private $winners;

	public function __construct( array $standings ) {
		$this->standings = $standings;
		$this->entry = 10;
		ksort( $this->standings );
		$this->winners = array_slice( $this->standings, 0, $this->get_cutoff(), true );
	}

	public function get_recommended_prize_pool() {
		return count( $this->standings ) * $this->entry * 0.85;
	}

	public function get_recommended_prize( $rank ) {
		if ( array_key_exists( $rank, $this->winners ) ) {
			$winner = $this->winners[$rank];
			return floor( (
					($winner['points'] + $this->get_rank_points( $rank )) /
					$this->get_total_points() * $this->get_recommended_prize_pool())
			);
		} else {
			return 0;
		}
	}

	private function get_total_points() {
		$total_match_points = 0;
		foreach ( $this->winners as $winner ) {
			$total_match_points += $winner['points'];
		}
		return $total_match_points + count( $this->winners ) * (count( $this->winners ) - 1);
	}

	private function get_rank_points( $rank ) {
		return 2 * (count( $this->winners ) - $rank);
	}

	private function get_cutoff() {
		return floor( count( $this->standings ) / 2 ) - 1;
	}

	public function get_recommended_league_points( $standing ) {
		return floor( $standing['points'] / 3 );
	}
}