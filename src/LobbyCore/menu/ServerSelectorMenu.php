<?php

namespace LobbyCore\menu;


use LobbyCore\player\NPlayer;
use pocketmine\utils\TextFormat;

class ServerSelectorMenu implements MenuInterface
{

    /** @var array $formData */
    private $formData = [];

    /** @var $menuManager */
    private $menuManager;

    public function __construct(MenuManager $manager)
    {
        $this->menuManager = $manager;

        $this->formData['type'] = 'form';
        $this->formData['buttons'][] = ['text' => TextFormat::RED . TextFormat::BOLD . "SurvivalGames"];
        $this->formData['buttons'][] = ['text' => 'Set default server'];
        $this->formData['title'] = 'Server Selector';
        $this->formData['content'] = '';
    }

    /**
     * @return int
     */
    public function getFormId(): int
    {
        return 1;
    }

    /**
     * @return array
     */
    public function getFormData(): array
    {
        return $this->formData;
    }

    /**
     * @param string $data
     * @param NPlayer $player
     */
    public function handleResponse(string $data, NPlayer $player): void
    {
        $data = json_decode($data, true);
        if(is_null($data)){
            return;
        }
        switch($data){
            case 0;
            $player->getServer()->dispatchCommand($player, "server survivalgames");
            break;
            case 1;
            $player->sendMenu($this->menuManager->getMenu('DefaultServerMenu'));
            break;
        }
    }

}