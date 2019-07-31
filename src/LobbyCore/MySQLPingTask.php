<?php

namespace LobbyCore;


use LobbyCore\utils\MySQLManager;
use pocketmine\scheduler\Task;

class MySQLPingTask extends Task
{

    /** @var MySQLManager $manager */
    private $manager;

    /**
     * MySQLPingTask constructor.
     * @param MySQLManager $manager
     */
    public function __construct(MySQLManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick)
    {
        $this->manager->getMysqli()->ping();
    }

}