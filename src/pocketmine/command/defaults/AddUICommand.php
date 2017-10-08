<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\command\defaults;

use pocketmine\inventory\customUI\elements\simpleForm\Button;
use pocketmine\inventory\customUI\windows\SimpleForm;
use pocketmine\inventory\customUI\CustomUI;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\TranslationContainer;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class AddUICommand extends VanillaCommand{

    public function __construct($name){
        parent::__construct(
            $name,
            "%pocketmine.command.addui.description",
            "%commands.addui.usage"
        );
        $this->setPermission("pocketmine.command.addui");
    }

    public function execute(CommandSender $sender, $currentAlias, array $args){
        if(!$this->testPermission($sender)){
            return true;
        }
        
        $this->server = Server::getInstance();
        
        $player = $this->server->getPlayer($args[0]);
        
        if(count($args) < 1){
        	$sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->usageMessage]));
            return false;
        }
        
        $form = new SimpleForm("Test");
        $player->sendModalForm($form);
        $button = new Button("Button");
        $form->addButton($button);
        
        return true;
    }
}