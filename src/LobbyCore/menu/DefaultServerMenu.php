<?php

namespace LobbyCore\menu;


use LobbyCore\Loader;
use LobbyCore\player\NPlayer;
use pocketmine\utils\TextFormat;

class DefaultServerMenu implements MenuInterface
{

    /** @var array $formData */
    private $formData = [];

    /** @var $menuManager */
    private $menuManager;

    const SERVER_LIST = [
        0 => "SkyWars",
        1 => "Survival Games"
    ];

    public function __construct(MenuManager $manager)
    {
        $this->menuManager = $manager;

        $this->formData['type'] = 'form';
        $this->formData['buttons'][] = ['text' => 'SurvivalGames'];
        $this->formData['title'] = 'Server Selector';
        $this->formData['content'] = '';
    }

    public function getFormId(): int
    {
        return 2;
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
        $data = json_decode($data);
        if(is_null($data) || !in_array(intval($data), array_keys(self::SERVER_LIST)))return;
        $player->sendMessage(TextFormat::BOLD . "Default server has been set to " . self::SERVER_LIST[intval($data)]);
        $server = strtolower(str_replace(" ", "", self::SERVER_LIST[intval($data)]));
        $this->menuManager->getPlugin()->getSQLManager()->getMysqli()->query("UPDATE players SET defaultServer='" . $server . "' WHERE username='" . $player->getName() . "'");

    }

}