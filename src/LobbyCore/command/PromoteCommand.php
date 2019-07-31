<?php

namespace LobbyCore\command;


use LobbyCore\player\NPlayer;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

class PromoteCommand extends PluginCommand
{

    public function __construct(Plugin $owner)
    {
        parent::__construct("promote", $owner);
        parent::setDescription("Promote player");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender instanceof NPlayer)return;

        if(count($args) < 1){
            $sender->sendMessage("Usage: /promote [name] [rank]");
        }

        $this->getPlugin()->getAccountManager()->setRank($args[0], $args[1]);
    }

}