<?php

abstract class List_Table
{
	protected $items;

	public function prepare_items() {
		$this->items = $this->get_items();
	}

	abstract protected function get_items();

	public function has_items() {
		return ! empty($this->items);
	}

	public function no_items() {
		_e( 'No items found.' );
	}

	protected abstract function get_all_columns();

	protected function get_hidden_columns() {
		return array_merge( array('id'), $this->get_grouped_columns() );
	}

	protected function get_sortable_columns() {
		return array();
	}

	protected function get_grouped_columns() {
		return array();
	}

	protected function get_column_widths() {
		return array();
	}

	protected function sort() {

	}

	private function get_column_info() {
		return array(
			$this->get_all_columns(),
			$this->get_hidden_columns(),
			$this->get_sortable_columns(),
			$this->get_grouped_columns(),
			$this->get_column_widths());
	}

	private function get_column_count() {
		list ($columns, $hidden) = $this->get_column_info();
		$hidden = array_intersect( array_keys( $columns ), array_filter( $hidden ) );
		return count( $columns ) - count( $hidden );
	}

	private function has_grouping() {
		return count( $this->get_grouped_columns() ) > 0;
	}

	public function display() {
		$this->display_tablenav( 'top' );

		?>
		<table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>">
			<thead>
			<tr>
				<?php $this->print_column_headers(); ?>
			</tr>
			</thead>

			<tfoot>
			<tr>
				<?php $this->print_column_headers( false ); ?>
			</tr>
			</tfoot>

			<tbody id="the-list">
			<?php $this->display_rows_or_placeholder(); ?>
			</tbody>
		</table>
		<?php
		$this->display_tablenav( 'bottom' );
	}

	private function print_column_headers( $with_id = true ) {
		list($columns, $hidden, $sortable, $grouped, $widths) = $this->get_column_info();

		foreach ( $columns as $column_key => $column_display_name ) {
			$class = array("column-$column_key");

			$style = '';
			if ( in_array( $column_key, $hidden ) ) {
				$style = 'display:none;';
			} else if ( isset($widths[$column_key]) ) {
				$style = 'width:' . $widths[$column_key];
			}

			if ( strlen( $style ) > 0 ) {
				$style = ' style="' . $style . '"';
			}

			$id = $with_id ? "id='$column_key'" : '';

			if ( ! empty($class) ) {
				$class = "class='" . join( ' ', $class ) . "'";
			}

			printf( '<th scope="col" %1$s, %2$s, %3$s>%4$s</th>', $id, $class, $style, $column_display_name );
		}
	}

	private function get_table_classes() {
		return array('widefat', 'fixed');
	}

	private function display_tablenav( $which ) {
		?>
		<div class="tablenav <?php echo esc_attr( $which ); ?>">
			<br class="clear"/>
		</div>
	<?php
	}

	private function display_rows_or_placeholder() {
		if ( $this->has_items() ) {
			$this->display_rows();
		} else {
			echo '<tr class="no-items"><td class="colspanchange" colspan="' . $this->get_column_count() . '">';
			$this->no_items();
			echo '</td></tr>';
		}
	}

	private function display_rows() {
		if ( $this->has_grouping() ) {
			$this->sort();

			$groups = array();
			$current_group = array($this->items[0]);

			for ( $i = 1; $i < count( $this->items ); $i ++ ) {
				$prev = $this->items[$i - 1];
				$item = $this->items[$i];
				if ( $this->is_same_group( $item, $prev ) ) {
					array_push( $current_group, $item );
				} else {
					array_push( $groups, $current_group );
					$current_group = array($item);
				}
			}
			array_push( $groups, $current_group );
			foreach ( $groups as $group ) {
				$this->group_header( $group );
				foreach ( $group as $item ) {
					$this->single_row( $item );
				}
			}
		} else {
			foreach ( $this->items as $item ) {
				$this->single_row( $item );
			}
		}
	}

	private function single_row( Model $item ) {
		static $row_class = '';
		$row_class = ($row_class == '' ? ' class="alternate"' : '');

		echo '<tr' . $row_class . '>';
		$this->single_row_columns( $item );
		echo '</tr>';
	}

	private function single_row_columns( Model $item ) {
		list($columns, $hidden) = $this->get_column_info();

		foreach ( $columns as $column_name => $column_display_name ) {
			$class = "class='$column_name column-$column_name'";

			$style = '';
			if ( in_array( $column_name, $hidden ) )
				$style = ' style="display:none;"';

			$attributes = "$class$style";

			printf( '<td %1$s> %2$s</td>', $attributes, $this->single_row_column_value( $column_name, $item ) );
		}
	}

	private function single_row_column_value( $column_name, Model $item ) {
		if ( method_exists( $this, 'column_' . $column_name ) ) {
			return call_user_func( array($this, 'column_' . $column_name), $item );
		} elseif ( method_exists( $item, 'get' . ucfirst( $column_name ) ) ) {
			return call_user_func( array($item, 'get' . ucfirst( $column_name )), $item );
		} else {
			return '';
		}
	}

	private function is_same_group( Model $item, Model $prev ) {
		$grouping = $this->get_grouped_columns();
		foreach ( $grouping as $column ) {
			if ( $this->single_row_column_value( $column, $item ) != $this->single_row_column_value( $column, $prev ) ) {
				return false;
			}
		}
		return true;
	}

	private function group_header( array $group ) {
		printf( '<tr class="group"><th colspan="%1$s">%2$s</th></tr>',
			$this->get_column_count(),
			$this->get_group_header_value( $group[0] )
		);
	}

	protected function get_group_header_value( Model $item ) {
		$result = '';
		foreach ( $this->get_grouped_columns() as $grouping ) {
			$result .= $this->single_row_column_value($grouping, $item) . ' ';
		}
		return $result;
	}
}
