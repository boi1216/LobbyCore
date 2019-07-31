<?php

namespace LobbyCore;


use LobbyCore\command\CoinsCommand;
use LobbyCore\command\HubCommand;
use LobbyCore\command\PromoteCommand;
use LobbyCore\menu\MenuManager;
use LobbyCore\player\NPlayer;
use LobbyCore\utils\AccountManager;
use LobbyCore\utils\MySQLManager;
use LobbyCore\utils\QuestManager;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;

class Loader extends PluginBase
{

    /** @var $mysqlManager */
    private $mysqlManager;

    /** @var $accountManager */
    private $accountManager;

    /** @var MenuManager $menuManager */
    private $menuManager;

    /** @var QuestManager $questManager */
    private $questManager;

    /** @var string $serverType */
    private $serverType;

    /** @var string $serverIdentifier */
    private $serverIdentifier;

    public function onEnable() : void
    {
        $level = $this->getServer()->getDefaultLevel();
        $level->setTime(500);
        $level->stopTime();

        $this->mysqlManager = new MySQLManager();
        $this->mysqlManager->init();
        $this->getScheduler()->scheduleRepeatingTask(new MySQLPingTask($this->mysqlManager), 600);

        $this->menuManager = new MenuManager();
        $this->menuManager->init($this);

        $this->accountManager = new AccountManager($this->mysqlManager);
        $this->questManager = new QuestManager($this);

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);

        $this->saveDefaultConfig();
        $this->serverType = $this->getConfig()->get('server-type');
        $this->serverIdentifier = $this->getConfig()->get('server-identifier');

        $this->getServer()->getNetwork()->setName(TextFormat::GOLD . "Netsword " . TextFormat::WHITE . $this->serverType);

        $this->getServer()->getCommandMap()->register("netsword", new PromoteCommand($this));
        $this->getServer()->getCommandMap()->register("netsword", new CoinsCommand($this));
        $this->getServer()->getCommandMap()->register("netsword", new HubCommand($this));

        $toUnregister = ['version', 'spawnpoint'];
        $commandMap = $this->getServer()->getCommandMap();
        foreach($toUnregister as $command){
            $command = $commandMap->getCommand($command);
            $command->setLabel("__disabled");
            $commandMap->unregister($command);
        }


    }

    public function getServerType() : string{

    }

    /**
     * @return MySQLManager
     */
    public function getSQLManager() : MySQLManager{
        return $this->mysqlManager;
    }

    /**
     * @return AccountManager
     */
    public function getAccountManager() : AccountManager{
        return $this->accountManager;
    }

    /**
     * @return MenuManager
     */
    public function getMenuManager() : MenuManager{
        return $this->menuManager;
    }

    /**
     * @return QuestManager
     */
    public function getQuestManager() : QuestManager{
        return $this->questManager;
    }

    /**
     * @param Player $player
     */
    public function giveJoinItems(Player $player) : void{
        $player->getInventory()->clearAll();
        $player->getInventory()->setItem(1, Item::get(Item::COMPASS)->setCustomName(TextFormat::RESET . TextFormat::AQUA . "Game Selector"));
    }

    /**
     * @param NPlayer $player
     */
    public function flyOnRedTile(NPlayer $player) {
        //front tile
        if ($player->getX() > (-37 -1) &&
            $player->getX() < (-37 + 1) &&
            $player->getZ() > (18 - 1) &&
            $player->getZ() < (18 + 1)) {

            $player->setMotion(new Vector3(1.8, 0.3, 0));
            return;
        }
        if ($player->getZ() > (27 - 1) &&
            $player->getZ() < (27 + 1)) {
            //right tile
            if ($player->getX() > (-45 - 1) &&
                $player->getX() < (-45 + 1)) {
                $player->setMotion(new Vector3(0, 0.3, 1.8));
                return;
            }
            //left tile
        }
        if($player->getX() < (-46 + 1) &&
           $player->getX() > (-46 - 1) &&
           $player->getZ() < (10 + 1) &&
           $player->getZ() > (10 - 1)){
           $player->setMotion(new Vector3(0, 0.3, -2));
        }
    }


}