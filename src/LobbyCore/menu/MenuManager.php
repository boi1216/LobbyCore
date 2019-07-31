<?php

namespace LobbyCore\menu;


use LobbyCore\Loader;

class MenuManager
{

    /** @var array $menus */
    private $menus = [];

    /** @var Loader $plugin */
    private $plugin;

    public function init(Loader $plugin) : void{
        $this->plugin = $plugin;
        $this->menus['ServerSelector'] = new ServerSelectorMenu($this);
        $this->menus['DefaultServerMenu'] = new DefaultServerMenu($this);
    }

    /**
     * @return Loader
     */
    public function getPlugin() : Loader{
        return $this->plugin;
    }

    /**
     * @param string $name
     * @return MenuInterface
     */
    public function getMenu(string $name) : MenuInterface{
        return $this->menus[$name];
    }

    /**
     * @return array
     */
    public function getMenuList() : array{
        return $this->menus;
    }

}