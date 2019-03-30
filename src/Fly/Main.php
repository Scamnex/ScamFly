<?php
namespace Fly;

use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\CommandExecutor;

class Main extends PluginBase implements Listener {

    public $players = array();

     public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info(TextFormat::GREEN . "Aktiviert!");
        $this->saveResource("config.yml");
     }



    public function onJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        if ($player->hasPermission("fly.use")) {

        }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if(strtolower($command->getName()) == "fly") {
            if($sender instanceof Player) {
                if($this->isPlayer($sender)) {
                    $settings = new Config($this->getDataFolder() . "config.yml", Config::YAML); 
                    $flyon = $settings->get("fly_on"); 
                    $flyoff = $settings->get("fly_off");
                    $pvpfly = $settings->get("pvp_fly");
                    $pvpmessage = $settings->get("pvp_message");
                    $this->removePlayer($sender);
                    $sender->setAllowFlight(false);
                    $sender->sendMessage($flyoff);
                    return true;
                }
                else{
                    $this->addPlayer($sender);
                    $sender->setAllowFlight(true);
                    $sender->sendMessage($flyon);
                    return true;
                }
            }
            else{
                $sender->sendMessage(TextFormat::RED . "command ingame benutzen");
                return true;
            }
        }
    }
    public function addPlayer(Player $player) {
        $this->players[$player->getName()] = $player->getName();
    }
    public function isPlayer(Player $player) {
        return in_array($player->getName(), $this->players);
    }
    public function removePlayer(Player $player) {
        unset($this->players[$player->getName()]);
    }
}

public function onEntityDamage(EntityDamageEvent $event) {
        if($event instanceof EntityDamageByEntityEvent) {
        $damager = $event->getDamager();
           if($damager instanceof Player && $this->isPlayer($damager)) {
              $damager->sendTip($pvpmessage);
              $event->setCancelled($pvpfly);
           }
        }
     }


     public function onDisable() {
        $this->getLogger()->info(TextFormat::RED . "Deaktiviert!");
     }
