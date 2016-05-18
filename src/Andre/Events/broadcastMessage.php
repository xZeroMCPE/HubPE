<?php
namespace RagingPE\Events;

use pocketmine\scheduler\PluginTask;
use pocketmine\Server;
use pocketmine\plugin\Plugin;

class Task extends PluginTask{
     public function __construct(Plugin $owner){
          parent::__construct($owner);
          $this->plugin = $owner;
     }
     public function onRun($currentTick){
          $broadcast = $this->plugin->this->broadcast;
          $message = $broadcast->get("Messages");
          $messages = $message[array_rand($message)];
          Server::getInstance()->broadcastMessage($messages);
     }
}
