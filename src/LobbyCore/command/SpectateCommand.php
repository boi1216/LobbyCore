<?php

namespace LobbyCore\command;


use LobbyCore\player\NPlayer;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

class SpectateCommand extends PluginCommand
{

    public function __construct(Plugin $owner)
    {
        parent::__construct("spectate", $owner);
        parent::setDescription("Spectate suspicious player");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if(!$sender instanceof NPlayer)return;

        if($sender->getState() == NPlayer::STATE_INGAME){
            $sender->sendMessage(TextFormat::RED . "You can use this command only if not in game!");
            return;
        }

        if($sender->getSpectating() !== null){
            $sender->spectate(null);
            $sender->teleport($sender->getServer()->getDefaultLevel()->getSafeSpawn());
            return;
        }

        if(empty($args[0])){
            return;
        }

        $player = $this->getPlugin()->getServer()->getPlayerExact($args[0]);
        if(!$player instanceof Player){
            $sender->sendMessage(TextFormat::RED . "Player not found!");
            return;
        }

        $sender->spectate($player);


    }

}