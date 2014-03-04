<?php

abstract class List_Table
{
    protected $items;
    private $index;
    private $key;

    public function prepare_items() {
        $this->items = $this->get_items();
    }

    abstract protected function get_items();

    public function has_items() {
        return ! empty( $this->items );
    }

    public function no_items() {
        return __( 'No items found.' );
    }

    protected abstract function get_all_columns();

    protected function get_hidden_columns() {
        return array_merge( array( 'id' ), $this->get_grouped_columns() );
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
            $this->get_column_widths() );
    }

    private function get_column_count() {
        list ( $columns, $hidden ) = $this->get_column_info();
        $hidden = array_intersect( array_keys( $columns ), array_filter( $hidden ) );
        return count( $columns ) - count( $hidden );
    }

    private function has_grouping() {
        return count( $this->get_grouped_columns() ) > 0;
    }

    public function display( $echo = true, $footer = true ) {
        $result = sprintf( '%s
		<table class="wp-list-table %s">
			<thead>
			<tr>
				%s
			</tr>
			</thead>
			<tfoot>
			<tr>
				%s
			</tr>
			</tfoot>
			<tbody id="the-list">
				%s
			</tbody>
		</table>
		%s',
            $this->get_tablenav( 'top' ),
            implode( ' ', $this->get_table_classes() ),
            $this->get_column_headers(),
            $footer ? $this->get_column_headers( false ) : '',
            $this->get_rows_or_placeholder(),
            $this->get_tablenav( 'bottom' )
        );

        if ( $echo ) {
            echo $result;
        }
        return $result;
    }

    private function get_column_headers( $with_id = true ) {
        list( $columns, $hidden, $sortable, $grouped, $widths ) = $this->get_column_info();

        $result = '';
        foreach ( $columns as $column_key => $column_display_name ) {
            $class = array( "column-$column_key" );

            $style = '';
            if ( in_array( $column_key, $hidden ) ) {
                $style = 'display:none;';
            } else if ( isset( $widths[$column_key] ) ) {
                $style = 'width:' . $widths[$column_key];
            }

            if ( strlen( $style ) > 0 ) {
                $style = ' style="' . $style . '"';
            }

            $id = $with_id ? "id='$column_key'" : '';

            if ( ! empty( $class ) ) {
                $class = "class='" . join( ' ', $class ) . "'";
            }

            $result .= sprintf( '<th scope="col" %1$s, %2$s, %3$s>%4$s</th>', $id, $class, $style, $column_display_name );
        }
        return $result;
    }

    protected function get_table_classes() {
        return array( 'widefat', 'fixed' );
    }

    private function get_tablenav( $which ) {
		$result = sprintf( '<div class="tablenav %s">', esc_attr( $which ) );
        if ( method_exists( $this, 'get_' . $which . '_tablenav' ) ) {
            $result .= call_user_func( array( $this, 'get_' . $which . '_tablenav' ) );
        }
        $result .= '</div>';
        return $result;
    }

    private function get_rows_or_placeholder() {
        if ( $this->has_items() ) {
            return $this->get_rows();
        } else {
            return sprintf( '<tr class="no-items"><td class="colspanchange" colspan="%u">%s</td></tr>',
                $this->get_column_count(), $this->no_items() );
        }
    }

    private function get_rows() {
        $this->index = 0;
        $result = '';
        $this->sort();
        if ( $this->has_grouping() ) {
            $groups = array();
            $current_group = array( $this->items[0] );

            for ( $i = 1; $i < count( $this->items ); $i ++ ) {
                $prev = $this->items[$i - 1];
                $item = $this->items[$i];
                if ( $this->is_same_group( $item, $prev ) ) {
                    array_push( $current_group, $item );
                } else {
                    array_push( $groups, $current_group );
                    $current_group = array( $item );
                }
            }
            array_push( $groups, $current_group );
            foreach ( $groups as $group ) {
                $result .= $this->group_header( $group );
                foreach ( $group as $item ) {
                    $result .= $this->single_row( $item );
                }
            }
        } else {
            foreach ( $this->items as $key => $item ) {
                $this->key = $key;
                $result .= $this->single_row( $item );
                $this->index ++;
            }
        }
        $this->index = null;
        $this->key = null;
        return $result;
    }

    private function single_row( $item ) {
        static $row_class = array();
        $row_class = ( empty( $row_class ) ? array( 'alternate' ) : array() );

        if ( method_exists( $this, 'get_row_classes' ) ) {
            $row_class = array_merge( $row_class, call_user_func( array( $this, 'get_row_classes' ), $item ) );
        }

        return sprintf( '<tr class="%s">%s</tr>', implode( ' ', $row_class ), $this->single_row_columns( $item ) );
    }

    private function single_row_columns( $item ) {
        list( $columns, $hidden ) = $this->get_column_info();
        $result = '';
        foreach ( $columns as $column_name => $column_display_name ) {
            $class = "class='$column_name column-$column_name'";

            $style = '';
            if ( in_array( $column_name, $hidden ) )
                $style = ' style="display:none;"';

            $attributes = "$class$style";

            $result .= sprintf( '<td %1$s> %2$s</td>', $attributes, $this->single_row_column_value( $column_name, $item ) );
        }
        return $result;
    }

    private function single_row_column_value( $column_name, $item ) {
        if ( method_exists( $this, 'column_' . $column_name ) ) {
            return call_user_func( array( $this, 'column_' . $column_name ), $item );
        } elseif ( method_exists( $item, 'get' . ucfirst( $column_name ) ) ) {
            return call_user_func( array( $item, 'get' . ucfirst( $column_name ) ), $item );
        } elseif ( method_exists( $item, 'get_' . $column_name ) ) {
            return call_user_func( array( $item, 'get_' . $column_name ), $item );
        } elseif ( is_array( $item ) && array_key_exists( $column_name, $item ) ) {
            return $item[$column_name];
        } else {
            return '';
        }
    }

    private function is_same_group( $item, $prev ) {
        $grouping = $this->get_grouped_columns();
        foreach ( $grouping as $column ) {
            if ( $this->single_row_column_value( $column, $item ) != $this->single_row_column_value( $column, $prev ) ) {
                return false;
            }
        }
        return true;
    }

    private function group_header( array $group ) {
        return sprintf( '<tr class="group"><th colspan="%1$s">%2$s</th></tr>',
            $this->get_column_count(),
            $this->get_group_header_value( $group[0] )
        );
    }

    protected function get_group_header_value( $item ) {
        $result = '';
        foreach ( $this->get_grouped_columns() as $grouping ) {
            $result .= $this->single_row_column_value( $grouping, $item ) . ' ';
        }
        return $result;
    }

    protected function get_index() {
        return $this->index;
    }

    protected function get_key() {
        return $this->key;
    }

    protected function row_actions( $edit_url, $delete_url ) {
        return sprintf( '<div class="row-actions"><span class="edit"><a href="%1$s">%2$s</a></span> |
		<span class="delete"><a href="%3$s">%4$s</a></span></div>',
            $edit_url,
            __( 'Edit' ),
            $delete_url,
            __( 'Delete' ) );
    }
}
