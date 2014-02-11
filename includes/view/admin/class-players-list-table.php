<?php

require_once LEAGUE_PLUGIN_DIR . 'includes/view/class-list-table.php';

class Players_List_Table extends List_Table
{
    private $players;

    public function __construct(Players $players)
    {
        $this->players = $players;
    }

    protected function get_items()
    {
        return $this->players->get_all();
    }

    protected function get_all_columns()
    {
        return array(
            'id' => 'ID',
            'name' => __('Name', 'league'),
            'dci' => __('DCI', 'league'),
            'credits' => __('Credits', 'league')
        );
    }

    protected function column_name(Player $player)
    {
        $query = array(
            'page' => 'players',
            'action' => 'display',
            'id' => $player->get_id()
        );

        return sprintf('<a href="%s">%s %s</a>', esc_url(admin_url('admin.php') . '?' . http_build_query($query)),
            $player->getFirst(), $player->getLast());
    }
}