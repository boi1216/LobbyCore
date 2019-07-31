<?php

namespace LobbyCore\player;


use LobbyCore\menu\MenuInterface;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class NPlayer extends \synapsepm\Player
{

    const STATE_LOBBY = 0;
    const STATE_INGAME = 1;

    /** @var int $state */
    private $state = self::STATE_LOBBY;

    /** @var int $chatInterval */
    private $chatInterval = 0;

    /** @var string $spectating */
    private $spectating;

    /**
     * @param Player $player
     */
    public function spectate(Player $player) : void{
        $this->spectating = $player->getName();
    }

    /**
     * @return null|Player
     */
    public function getSpectating() : ?Player{
        return $this->getServer()->getPlayerExact($this->spectating);
    }

    /**
     * @return int
     */
    public function getState() : int{
        return $this->state;
    }

    /**
     * @param int $state
     * @throws \Exception
     */
    public function setState(int $state) : void{
        if($state > 1){
            throw new \Exception("Invalid game state has been sent to player " . $this->id);
        }
        $this->state = $state;
    }

    /**
     * @param string $message
     * @return bool
     */
    public function chat(string $message): bool
    {
        if(time() - $this->chatInterval < 4){
            $this->sendMessage(TextFormat::RED . "Spamming is against the rules!");
            return false;
        }
        $advertisment = ['.eu', '.net', '.com', '.tk', '.biz'];
        if(str_replace($advertisment, "", $message) !== $message){
            $this->sendMessage(TextFormat::RED . "Please do not advertise!");
            return false;
        }
        $this->chatInterval = time();
        return parent::chat($message);
    }

    /**
     * @param MenuInterface $menu
     */
    public function sendMenu(MenuInterface $menu) : void{
        $formId = $menu->getFormId();
        $formData = $menu->getFormData();

        $packet = new ModalFormRequestPacket();
        $packet->formId = $formId;
        $packet->formData = json_encode($formData);

        $this->dataPacket($packet);
    }


}