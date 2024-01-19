<?php

#      _       ____   __  __ 
#     / \     / ___| |  \/  |
#    / _ \   | |     | |\/| |
#   / ___ \  | |___  | |  | |
#  /_/   \_\  \____| |_|  |_|
# The creator of this plugin was fernanACM.
# https://github.com/fernanACM

namespace fernanACM\AntiFires;

use pocketmine\Server;
use pocketmine\player\Player;

use pocketmine\event\block\BlockPlaceEvent;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;

use pocketmine\event\player\PlayerInteractEvent;

use pocketmine\item\VanillaItems;

use pocketmine\utils\Config;

class AntiFires extends PluginBase implements Listener{

     /** @var Config $config */
     public Config $config;

    private const BLACKLIST = "blacklist";
    private const WHITELIST = "whitelist";

    /**
     * @return void
     */
    public function onEnable(): void{
        $this->loadFiles();
        Server::getInstance()->getPluginManager()->registerEvents($this, $this);
    }

    /**
     * @return void
     */
    private function loadFiles(): void{
        $this->saveResource("config.yml");
        $this->config = new Config($this->getDataFolder() . "config.yml");
    }

    /**
     * @param PlayerInteractEvent $event
     * @return void
     */
    public function onIntetact(PlayerInteractEvent $event): void{
        $player = $event->getPlayer();
        if($this->getWorldsEnabled($player)){
            if($player->getInventory()->getItemInHand()->getTypeId() === VanillaItems::FLINT_AND_STEEL()->getTypeId()){
                $event->cancel();
            }
            if($player->getInventory()->getItemInHand()->getTypeId() === VanillaItems::LAVA_BUCKET()->getTypeId()){
                $event->cancel();
            }
        }
    }

    /**
     * @param BlockPlaceEvent $event
     * @return void
     */
    public function onPlace(BlockPlaceEvent $event): void{
        $player = $event->getPlayer();
        if($this->getWorldsEnabled($player)){
            if($player->getInventory()->getItemInHand()->getTypeId() === VanillaItems::LAVA_BUCKET()->getTypeId()){
                $event->cancel();
            }
        }
    }

    /**
     * @param Player $player
     * @return boolean
     */
    public function getWorldsEnabled(Player $player): bool{
        $mode = $this->config->getNested("Settings.WorldManager.mode");
        if(boolval($mode) === false){
            return true;
        }
        switch(strtolower($mode)){
            case self::BLACKLIST:
            return $this->isBlacklistMode($player->getWorld()->getFolderName());
    
            case self::WHITELIST:
            return $this->isWhitelistMode($player->getWorld()->getFolderName());
        }
    }
    
    /**
     * @param string $worldName
     * @return boolean
     */
    public function isWhitelistMode(string $worldName): bool{
        $worldsWhitelist = $this->config->getNested("Settings.WorldManager.worlds-whitelist");
        return in_array($worldName, $worldsWhitelist);
    }
    
    /**
     * @param string $worldName
     * @return boolean
     */
    public function isBlacklistMode(string $worldName): bool{
        $worldsBlacklist = $this->config->getNested("Settings.WorldManager.worlds-blacklist");
        return !in_array($worldName, $worldsBlacklist);
    }
}