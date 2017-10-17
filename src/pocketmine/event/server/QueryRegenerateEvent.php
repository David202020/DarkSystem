<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\event\server;

use pocketmine\event;
use pocketmine\Server;
use pocketmine\utils\Binary;

class QueryRegenerateEvent extends ServerEvent{
	
	public static $handlerList = null;

	const GAME_ID = "MINECRAFTPE"; //OMG I WAS KNOW IT AS LOL

	private $timeout;
	private $serverName;
	private $listPlugins;
	private $plugins;
	private $players;

	private $gametype;
	private $version;
	private $server_engine;
	private $map;
	private $numPlayers;
	private $maxPlayers;
	private $whitelist;
	private $port;
	private $ip;

	private $extraData = [];


	public function __construct(Server $server, $timeout = 7){
		$this->timeout = $timeout;
		$this->serverName = $server->getServerName();
		$this->listPlugins = $server->getProperty("settings.query-plugins", false);
		$this->plugins = $server->getPluginManager()->getPlugins();
		$this->players = [];
		foreach($server->getOnlinePlayers() as $player){
			if($player->isOnline()){
				$this->players[] = $player;
			}
		}

		$this->gametype = ($server->getGamemode() & 0x01) === 0 ? "SMP" : "CMP";
		$this->version = $server->getVersion();
		$this->server_engine = $server->getName() . " " . $server->getPocketMineVersion();
		$this->map = $server->getDefaultLevel() === null ? "unknown" : $server->getDefaultLevel()->getName();
		$this->numPlayers = count($this->players);
		$this->maxPlayers = $server->getMaxPlayers();
		$this->whitelist = $server->hasWhitelist() ? "on" : "off";
		$this->port = $server->getPort();
		$this->ip = $server->getIp();
	}
	
	public function getTimeout(){
		return $this->timeout;
	}

	public function setTimeout($timeout){
		$this->timeout = $timeout;
	}

	public function getServerName(){
		return $this->serverName;
	}

	public function setServerName($serverName){
		$this->serverName = $serverName;
	}

	public function canListPlugins(){
		return $this->listPlugins;
	}

	public function setListPlugins($value){
		$this->listPlugins = (bool) $value;
	}
	
	public function getPlugins(){
		return $this->plugins;
	}
	
	public function setPlugins(array $plugins){
		$this->plugins = $plugins;
	}
	
	public function getPlayerList(){
		return $this->players;
	}
	
	public function setPlayerList(array $players){
		$this->players = $players;
	}

	public function getPlayerCount(){
		return $this->numPlayers;
	}

	public function setPlayerCount($count){
		$this->numPlayers = (int) $count;
	}

	public function getMaxPlayerCount(){
		return $this->maxPlayers;
	}

	public function setMaxPlayerCount($count){
		$this->maxPlayers = (int) $count;
	}

	public function getWorld(){
		return $this->map;
	}

	public function setWorld($world){
		$this->map = (string) $world;
	}
	
	public function getExtraData(){
		return $this->extraData;
	}

	public function setExtraData(array $extraData){
		$this->extraData = $extraData;
	}

	public function getLongQuery(){
		$query = "";

		$plist = $this->server_engine;
		if(count($this->plugins) > 0 and $this->listPlugins){
			$plist .= ":";
			foreach($this->plugins as $p){
				$d = $p->getDescription();
				$plist .= " " . str_replace([";", ":", " "], ["", "", "_"], $d->getName()) . " " . str_replace([";", ":", " "], ["", "", "_"], $d->getVersion()) . ";";
			}
			$plist = substr($plist, 0, -1);
		}

		$KVdata = [
			"splitnum" => chr(128),
			"hostname" => $this->serverName,
			"gametype" => $this->gametype,
			"game_id" => self::GAME_ID,
			"version" => $this->version,
			"server_engine" => $this->server_engine,
			"plugins" => $plist,
			"map" => $this->map,
			"numplayers" => $this->numPlayers,
			"maxplayers" => $this->maxPlayers,
			"whitelist" => $this->whitelist,
			"hostip" => $this->ip,
			"hostport" => $this->port
		];

		foreach($KVdata as $key => $value){
			$query .= $key . "\x00" . $value . "\x00";
		}

		foreach($this->extraData as $key => $value){
			$query .= $key . "\x00" . $value . "\x00";
		}

		$query .= "\x00\x01player_\x00\x00";
		foreach($this->players as $player){
			$query .= $player->getName() . "\x00";
		}
		
		$query .= "\x00";

		return $query;
	}

	public function getShortQuery(){
		return $this->serverName . "\x00" . $this->gametype . "\x00" . $this->map . "\x00" . $this->numPlayers . "\x00" . $this->maxPlayers . "\x00" . Binary::writeLShort($this->port) . $this->ip . "\x00";
	}

}
