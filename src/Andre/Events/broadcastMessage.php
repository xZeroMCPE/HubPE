<?php
namespace Andre\Events;

use pocketmine\scheduler\PluginTask;
use pocketmine\Server;
use pocketmine\plugin\Plugin;

class broadcastMessage extends PluginTask{
     public function __construct(Plugin $owner){
          parent::__construct($owner);
          $this->plugin = $owner;
     }
     public function onRun($currentTick){
         // $broadcast = $this->plugin->this->broadcast;
         // $message = $broadcast->get("Messages");                    *removed for now*
        //  $messages = $message[array_rand($message)];
        $messages = $this->plugin->broadcast->get("Messages");
        $randommessage = $messages[array_rand($messages)]; 
          Server::getInstance()->broadcastMessage("$randommessage");
     }
}
