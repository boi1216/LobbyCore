<?php

namespace LobbyCore\menu;


use LobbyCore\player\NPlayer;
use pocketmine\Player;

interface MenuInterface
{

    public function handleResponse(string $data, NPlayer $player) : void;

    public function getFormId() : int;

    public function getFormData() : array;



}