<?php
namespace Andre;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
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
	public $flight = [];
	public $vanish = [];
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
		$e->setJoinMessage(""); //Removes join message
		$player->teleport($this->getServer()->getDefaultLevel()->getSafeSpawn());
	}
	public function onOuit(PlayerQuitEvent $e){
		$player = $e->getPlayer();
		$Quit_Message = $this->getConfig()->get("Quit_Message");
		$player->sendMessage("$Quit_Message");
		$e->setQuitMessage("");// Removes quit message
	}
	public function onChat(PlayerChatEvent $event){
		$message = $event->getMessage();
		$Disable_Commands = $this->getConfig()->get("Disable_Commands");
		if($message === $Disable_Commands){
			$event->setCancelled();
		}
	}
	public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
		switch ($command){
			case 'HubPE':
			if(empty($args[0])){
				$sender->sendMessage("/HubPE setlobby - Sets the main world spawn\n/HubPE fly - Ability to fly in survival\n/HubPE vanish - Ability to be invisible\n/HubPE unvanish - Ability to be visible\n/HubPE - Shows a list of HubPE commands ");
			} else {
				if($args[0] == "setlobby"){
					if($sender instanceof Player){
						if($sender->hasPermission("HubPE.setlobby")){
							$x = $sender->getX();
							$y = $sender->gety();
							$z = $sender->getZ();
							$this->config->set("LobbyX", "$x");
							$this->config->set("LobbyY", "$x");
							$this->config->set("LobbyZ", "$x");
							$sender->sendMessage("Lobby spawn has been set to your position");
						} else {
							$sender->sendMessage("§cYou don't have permission to use this command");
						}
					} else {
						$sender->sendMessage("Run this command in game");
					}
				}elseif($args[0] == "fly"){
					if($sender instanceof Player){
						if($sender->hasPermission("HubPE.fly")){
							if(isset($this->flight[$sender->getName()])){
								$sender->setAllowFlight(false);
								$sender->sendMessage("You have disabled flight mode");
								unset($this->flight[$sender->getName()]);
							} else {
								$sender->setAllowFlight(true);
								$sender->sendMessage("You have enabled flight mode");
								$this->flight[$sender->getName()] = $sender->getName();
							}
						} else {
							$sender->sendMessage("§cYou don't have permission to use this command");
						}
					} else {
						$sender->sendMessage("Run this command in game");
					}
				}elseif($args[0] == "unvanish"){
					if($sender instanceof Player){
						if($sender->hasPermission("HubPE.vanish")){
							if(isset($this->vanish[$sender->getName()])){
								foreach($this->getServer()->getOnlinePlayers() as $p){
									$p->showPlayer($sender);
									unset($this->vanish[$sender->getName()]);
									$sender->sendMessage("You are now visible");
								}
							}
						} else {
							$sender->sendMessage("§cYou don't have permission to use this command");
						}
					} else {
						$sender->sendMessage("Run this command in game");
					}
				}elseif($args[0] == "vanish"){
					if($sender instanceof Player){
						if($sender->hasPermission("HubPE.vanish")){
							if(isset($this->vanish[$sender->getName()])){
								foreach($this->getServer()->getOnlinePlayers() as $p){
									$p->hidePlayer($sender);
									$this->vanish[$sender->getName()] = $sender;
									$sender->sendMessage("You are now invisible");
								}
							}
						} else {
							$sender->sendMessage("§cYou don't have permission to use this command");
						}
					} else {
						$sender->sendMessage("Run this command in game");
					}
				} else {
					$sender->sendMessage("Missing Parameter(s)");
				}
			}
			break;
			case 'lobby':
			if($sender instanceof Player){
				$sender->teleport($this->getServer()->getDefaultLevel()->getSafeSpawn());
			} else {
				$sender->sendMessage("Run this command in game");
			}
			break;
			case 'vanish':
			if($sender instanceof Player){
				if($sender->hasPermission("HubPE.vanish")){
					foreach($this->getServer()->getOnlinePlayers() as $p){
						$p->hidePlayer($sender);
						$this->vanish[$sender->getName()] = $sender;
						$sender->sendMessage("You are now invisible");
					}
				} else {
					$sender->sendMessage("§cYou don't have permission to use this command");
				}
			} else {
				$sender->sendMessage("Run this command in game");
			}
			break;
			case 'unvanish':
			if(isset($this->vanish[$sender->getName()])){
				foreach($this->getServer()->getOnlinePlayers() as $p){
					$p->showPlayer($sender);
					unset($this->vanish[$sender->getName()]);
					$sender->sendMessage("You are now visible");
				}
			} else {
				$sender->sendMessage("You are not invisible. Do /vanish");
			}
			break;
		}
	}
}
