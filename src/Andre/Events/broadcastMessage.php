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
        $message = $owner->this->broadcast->get("Messages");
        $randommessage = array_rand($message); 
          Server::getInstance()->broadcastMessage("$randommessage");
     }
}
