<?php

require_once dirname(__FILE__) . '/class-admin-screen.php';

class Player_Screen extends Admin_Screen
{
    public function add_admin_menu()
    {
        add_submenu_page(
            'leagues',
            'Players',
            'Players',
            'publish_pages',
            'players',
            array($this, 'load_players_menu')
        );
    }

    public function load_players_menu()
    {
        switch ($this->current_action()) {
            case 'display':
                load_template(LEAGUE_PLUGIN_DIR . 'templates/player-display.php');
                break;
            default:
                load_template(LEAGUE_PLUGIN_DIR . 'templates/player-admin.php');
        }
    }
}