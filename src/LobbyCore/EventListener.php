<?php

namespace LobbyCore;


use LobbyCore\player\NPlayer;
use pocketmine\entity\Villager;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerJumpEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\item\Compass;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\ItemFrameDropItemPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\network\mcpe\protocol\TextPacket;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class EventListener implements Listener
{

    /** @var Loader $plugin */
    private $plugin;

    /**
     * EventListener constructor.
     * @param Loader $plugin
     */
    public function __construct(Loader $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @return Loader
     */
    public function getPlugin() : Loader{
        return $this->plugin;
    }

    /**
     * @param DataPacketReceiveEvent $event
     */
    public function onPacketReceive(DataPacketReceiveEvent $event) : void{
        $player = $event->getPlayer();
        if($event->getPacket() instanceof ItemFrameDropItemPacket){
            if($player->getState() == NPlayer::STATE_LOBBY){
                $event->setCancelled();
            }
        }elseif($event->getPacket() instanceof ModalFormResponsePacket){
            foreach($this->getPlugin()->getMenuManager()->getMenuList() as $menu){
                if($menu->getFormId() == $event->getPacket()->formId){
                    $menu->handleResponse($event->getPacket()->formData, $player);
                }
            }
        }
    }

    /**
     * @param PlayerDropItemEvent $event
     */
    public function onItemDrop(PlayerDropItemEvent $event) : void{
        $player = $event->getPlayer();
        if($player->getState() == NPlayer::STATE_LOBBY){
            $event->setCancelled();
        }
    }

    public function onBlockBreak(BlockBreakEvent $event) : void{
        $player = $event->getPlayer();
        if($player->getState() == NPlayer::STATE_LOBBY){
            $event->setCancelled();
        }
    }

    public function onBlockPlace(BlockPlaceEvent $event) : void{
        $player = $event->getPlayer();
        if($player->getState() == NPlayer::STATE_LOBBY){
            $event->setCancelled();
        }
    }

    /**
     * @param EntityDamageEvent $event
     */
    public function onDamage(EntityDamageEvent $event) : void{
        $entity = $event->getEntity();
        if($entity instanceof NPlayer && $entity->getState() == NPlayer::STATE_LOBBY){
            $event->setCancelled();
        }
        if($event instanceof EntityDamageByEntityEvent && $event->getDamager() instanceof NPlayer){
            if(explode( "\n", $entity->getNameTag())[0] == TextFormat::AQUA . "QuestKeeper"){
                $this->getPlugin()->getQuestManager()->pickQuest($event->getDamager());
            }
        }
    }

    /**
     * @param EntityLevelChangeEvent $event
     * @throws \Exception
     */
    public function onLevelChange(EntityLevelChangeEvent $event) : void{
        $entity = $event->getEntity();
        if(!$entity instanceof NPlayer)return;

        if($event->getTarget()->getId() !== $this->getPlugin()->getServer()->getDefaultLevel()->getId()){
            $entity->setState(NPlayer::STATE_INGAME);
        }else{
            $entity->setState(NPlayer::STATE_LOBBY);
            $this->getPlugin()->giveJoinItems($entity);
        }
    }

    /**
     * @param PlayerDeathEvent $event
     */
    public function onDeath(PlayerDeathEvent $event) : void{
        $event->setDeathMessage('');
        $lastCause = $event->getEntity()->getLastDamageCause();
        if($lastCause instanceof EntityDamageByEntityEvent && $lastCause->getDamager() instanceof Player){
            $questManager = $this->getPlugin()->getQuestManager();
            $questManager->checkQuest($lastCause->getDamager(), 1);
        }
    }

    /**
     * @param PlayerJoinEvent $event
     */
    public function onJoin(PlayerJoinEvent $event) : void{
        $player = $event->getPlayer();
        $event->setJoinMessage('');
        if($this->getPlugin()->getAccountManager()->isPlayerRegistred($player->getName())){
            $player->sendMessage(TextFormat::GREEN . "Welcome back, " . TextFormat::GOLD . $player->getName());
            $questManager = $this->getPlugin()->getQuestManager();
            $questManager->checkQuest($player, 2);
        }else{
            $player->sendMessage(TextFormat::GREEN . "Welcome, " . TextFormat::GOLD . $player->getName());
            $this->getPlugin()->getAccountManager()->registerPlayer($player->getName());
        }

        $player->sendMessage(TextFormat::YELLOW . "You are playing on " . TextFormat::WHITE . "Netsword " . $this->plugin->getConfig()->get('server-type'));
        $player->teleport($this->getPlugin()->getServer()->getDefaultLevel()->getSafeSpawn());
        $this->getPlugin()->giveJoinItems($player);
    }

    /**
     * @param PlayerCreationEvent $event
     */
    public function onPlayerClassCreation(PlayerCreationEvent $event) : void{
        $event->setPlayerClass(NPlayer::class);
    }

    /**
     * @param PlayerChatEvent $event
     */
    public function onChat(PlayerChatEvent $event) : void{
        $player = $event->getPlayer();
      //  if($player->getState() !== NPlayer::STATE_LOBBY)return;
        $rank = $this->getPlugin()->getAccountManager()->getPlayerData($player->getName())['rank'];
        $format = TextFormat::GRAY  . $player->getName() . " > " . $event->getMessage();
        switch($rank){
            case "Owner";
            $format = TextFormat::YELLOW . "Owner " . TextFormat::GRAY . $player->getName() . " > " . $event->getMessage();
            break;
            case "Admin";
            $format = TextFormat::RED . "Admin " . TextFormat::GRAY . $player->getName() . " > " . $event->getMessage();
            break;
            case "Builder";
            $format = TextFormat::GOLD . "Builder " . TextFormat::GRAY . $player->getName() . " > " . $event->getMessage();
            break;
            case "Moderator";
            $format = TextFormat::DARK_RED . "Mod " . TextFormat::GRAY . $player->getName() . " > " . $event->getMessage();
            break;

        }
        foreach($player->getLevel()->getPlayers() as $p){
            $p->sendMessage($format);
        }
        $this->getPlugin()->getLogger()->info($format);
        $event->setCancelled();

    }

    /**
     * @param PlayerInteractEvent $event
     */
    public function onInteract(PlayerInteractEvent $event) : void{
        $player = $event->getPlayer();
        if($player->getState() !== NPlayer::STATE_LOBBY)return;
        $item = $event->getItem();

        $menuManager = $this->getPlugin()->getMenuManager();

        if($item instanceof Compass){
            $player->sendMenu($menuManager->getMenu('ServerSelector'));
        }
    }

    public function onMove(PlayerMoveEvent $event) : void{
        if($event->getPlayer()->getState() == NPlayer::STATE_LOBBY){
            $this->getPlugin()->flyOnRedTile($event->getPlayer());
        }
      /*  $spectateTarget = $event->getPlayer()->getSpectating();
        if(!$spectateTarget instanceof NPlayer)return;

        $wishX = $spectateTarget->getX();
        $wishZ = $spectateTarget->getZ();
        $wishY = $spectateTarget->getY();
        $x = ($wishX - $event->getPlayer()->x) / 6.5235748291016;
        $y = ($wishX - $event->getPlayer()->y) / 6.5235748291016;
        $z = ($wishZ - $event->getPlayer()->z) / 6.5235748291016;
        $event->getPlayer()->addMotion($x, $y,$z);*/
    }

    /**
     * @param PlayerJumpEvent $event
     */
    public function onJump(PlayerJumpEvent $event) : void{
        $player = $event->getPlayer();
        $questManager = $this->getPlugin()->getQuestManager();
        $questManager->checkQuest($player, 0);
    }

    /**
     * @param PlayerExhaustEvent $event
     */
    public function onExhaust(PlayerExhaustEvent $event) : void{
        $player = $event->getPlayer();
        if(!$player instanceof Player)return;
        if($player->getState() == NPlayer::STATE_LOBBY)$event->setCancelled();
    }



}