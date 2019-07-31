<?php

namespace LobbyCore\command;


use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

class CoinsCommand extends PluginCommand
{

    /**
     * CoinsCommand constructor.
     * @param Plugin $owner
     */
    public function __construct(Plugin $owner)
    {
        parent::__construct("coins", $owner);
        parent::setDescription("Display your balance");
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

        $sender->sendMessage(TextFormat::GRAY . "Your coins: " . TextFormat::GREEN . $this->getPlugin()->getAccountManager()->getPlayerData($sender->getName())['coins']);
    }

}