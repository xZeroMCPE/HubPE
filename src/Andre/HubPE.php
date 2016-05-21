<?php
namespace Andre;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\Player;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\math\Vector3;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\utils\Config;

class HubPE extends PluginBase implements Listener {
	public $config;
	public $broadcast;
     public function onEnable(){
            $this->getServer()->getPluginManager()->registerEvents($this,$this);
			$this->saveResource("BroadcastMessage.yml");
			$this->saveDefaultConfig();
            $this->config = (new Config($this->getDataFolder()."config.yml", Config::YAML));
			$this->broadcast = (new Config($this->getDataFolder()."BroadcastMessage.yml", Config::YAML));
            $this->getServer()->getScheduler()->scheduleRepeatingTask(new Events\broadcastMessage($this), 400);
            $this->getLogger()->info("HubPE has been enabled!");
     }
     public function onBlockBreak(BlockBreakEvent $event){
      
        $player = $event->getPlayer();
          if(!$player->hasPermission("HubPE.break")){
            $event->setCancelled();
           $player->sendPopup("You are not allowed to break blocks here.");
          }
               }
     public function onBlockPlace(BlockPlaceEvent $event){
      
        $player = $event->getPlayer();
          if(!$player->hasPermission("HubPE.place")){
            $event->setCancelled();
           $player->sendPopup("You are not allowed to place blocks here.");
               }
       }
     public function onKick(PlayerKickEvent $event){
             if($event->getReason() === "disconnectionScreen.serverFull"){
                 if($event->getPlayer()->hasPermission("HubPE.full")){
                 $event->setCancelled(true);
                 }
             }
     }
     public function onJoin(PlayerJoinEvent $e){
            $player = $e->getPlayer();
            $Join_Message = $this->getConfig()->get("Join_Message");
            $player->sendMessage("$Join_Message");
            $player->teleport($this->getServer()->getDefaultLevel()->getSafeSpawn());
     }
     public function onChat(PlayerChatEvent $event)
		{
             $message = $event->getMessage();
             $Disable_Commands = $this->getConfig()->get("Disable_Commands");
             if($message === $Disable_Commands){
                  $event->setCancelled();
             }
		}
     public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
        switch ($command){
            case 'HubPE':
		$sender->sendMessage("/HubPE setlobby - Sets the main world spawn\n/HubPE - Shows a list of HubPE commands ");
                             if($args[0] == "HubPE" && $args[1] == "setlobby"){
                             $x = $sender->getX();
		                	 $y = $sender->gety();
                             $z = $sender->getZ();
                             $this->config->set("LobbyX", "$x");
                             $this->config->set("LobbyY", "$x");
                             $this->config->set("LobbyZ", "$x");
                             $sender->sendMessage("Lobby spawn has been set to your position");
                              if($args[0] == "lobby"){
                             	$sender->teleport($this->getServer()->getDefaultLevel()->getSafeSpawn());
							  }
							 }
							 }
							 
		}
	 }
