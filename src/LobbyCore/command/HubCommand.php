<?php

namespace LobbyCore\command;


use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;

class HubCommand extends PluginCommand
{

    /**
     * CoinsCommand constructor.
     * @param Plugin $owner
     */
    public function __construct(Plugin $owner)
    {
        parent::__construct("hub", $owner);
        parent::setDescription("Return to hub");
        parent::setAliases(["lobby"]);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return bool|mixed|void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if(!$sender instanceof Player)return;
        $sender->teleport($this->getPlugin()->getServer()->getDefaultLevel()->getSafeSpawn());
    }

}