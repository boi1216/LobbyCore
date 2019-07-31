<?php

namespace LobbyCore\utils;


use LobbyCore\Loader;
use LobbyCore\player\NPlayer;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class QuestManager
{

    /** @var Loader $plugin */
    private $plugin;

    const QUESTS = [
        0 => ["Jump 250 times", 250],
        1 => ["Kill 20 players", 20],
        2 => ["Join 50 times", 50]

    ];

    /**
     * QuestManager constructor.
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function hasQuestsFinished(Player $player) : bool{
if(count($this->getQuests($player)) > 1)return false;
return true;
}

    /**
     * @param string $username
     */
    public function writeQuests(string $username) : void{
        $quest = rand(0,2);
        $questArray = serialize(['questNum' => $quest, 'points' => 0]);
        $command = "INSERT INTO quests (username, quest) VALUES('$username', '$questArray')";
        $this->plugin->getSQLManager()->getMysqli()->query($command);
    }

    /**
     * @param Player $player
     * @return array
     */
    public function getQuests(Player $player) : array{
        $array = $this->plugin->getSQLManager()->fetchArrayByKey("quests", "username", $player->getName());
        $quests = $array['quest'];
        if(strlen($quests) < 1)return [];
        $quests = unserialize($quests);
        return $quests;
    }

    /**
     * @param NPlayer $player
     */
    public function pickQuest(NPlayer $player) : void{
        if(!$this->hasQuestsFinished($player)){
            $player->sendMessage(TextFormat::GREEN . "• " . TextFormat::GRAY . "You already have picked up a quest" . TextFormat::GREEN . "! " . TextFormat::GRAY . "It is:");
            $player->sendMessage(TextFormat::WHITE . self::QUESTS[$this->getQuests($player)['questNum']][0]);
            return;
        }
        $player->sendMessage(TextFormat::GREEN . "• " . TextFormat::GRAY . "Picking up the right quest for you" . TextFormat::GREEN . "...");
        $this->writeQuests($player->getName());
        $player->sendMessage(TextFormat::GREEN . "• " . TextFormat::GRAY . "Got it" . TextFormat::GREEN . "! " . TextFormat::WHITE . TextFormat::WHITE . self::QUESTS[$this->getQuests($player)['questNum']][0]);
    }

    /**
     * @param NPlayer $player
     */
    public function updateQuest(NPlayer $player) : void{
        $questData = $this->getQuests($player);
        $newData = serialize([
            'questNum' => $questData['questNum'],
            'points' => $questData['points'] + 1
        ]);
        $username = $player->getName();
        $command = "UPDATE quests SET quest='$newData' WHERE username='$username'";
        $this->plugin->getSQLManager()->getMysqli()->query($command);

        if($questData['points'] + 1 >= self::QUESTS[$questData['questNum']][1]){
            $player->sendMessage(TextFormat::GREEN . "• " . TextFormat::GRAY . "You have completed your quest!");
            $coinArray = [50, 100, 150, 200, 250, 300, 350, 400, 450, 500, 550, 600, 650, 700, 750, 800, 850, 900, 950, 1000];
            $coins = $coinArray[array_rand($coinArray)];
            $player->sendMessage(TextFormat::GREEN . "• " . TextFormat::GRAY . "Reward: " . TextFormat::GREEN .$coins .  " coins");
            $command = "UPDATE players SET coins=coins + '$coins' WHERE username='$username'";
            $command2 = "DELETE from quests WHERE username = '$username'";
            $this->plugin->getSQLManager()->getMysqli()->query($command);
            $this->plugin->getSQLManager()->getMysqli()->query($command2);
        }
    }

    /**
     * @param NPlayer $player
     * @param int $requiredQuestNum
     */
    public function checkQuest(NPlayer $player, int $requiredQuestNum) : void{
        if($this->hasQuestsFinished($player))return;
        $quest = $this->getQuests($player);
        if($quest['questNum'] == $requiredQuestNum){
            $this->updateQuest($player);
        }
    }

}