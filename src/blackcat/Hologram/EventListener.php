<?php

 #  _   _       _   _           _____             
 # | \ | |     | | (_)         |  __ \            
 # |  \| | __ _| |_ ___   _____| |  | | _____   __
 # | . ` |/ _` | __| \ \ / / _ \ |  | |/ _ \ \ / /
 # | |\  | (_| | |_| |\ V /  __/ |__| |  __/\ V / 
 # |_| \_|\__,_|\__|_| \_/ \___|_____/ \___| \_/  
 # Больше плагинов в https://vk.com/native_dev
 # По вопросам native.dev@mail.ru
 # Этот плагин форк(клон) плагина https://github.com/fuyutsuki/Texter

declare(strict_types = 1);

namespace blackcat\Hologram;

use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\Player;
use blackcat\Hologram\command\TxtCommand;
use blackcat\Hologram\i18n\Lang;
use blackcat\Hologram\task\SendTextsTask;
use blackcat\Hologram\text\Text;

class EventListener implements Listener {

  public function onJoin(PlayerJoinEvent $ev): void {
    $p = $ev->getPlayer();
    $l = $p->getLevel();
    $add = new SendTextsTask($p, $l);
    Core::get()->getScheduler()->scheduleDelayedRepeatingTask($add, 20, 1);
  }

  public function onLevelChange(EntityLevelChangeEvent $ev): void {
    $ent = $ev->getEntity();
    if ($ent instanceof Player) {
      $from = $ev->getOrigin();
      $to = $ev->getTarget();
      $core = Core::get();
      $remove = new SendTextsTask($ent, $from, Text::SEND_TYPE_REMOVE);
      $core->getScheduler()->scheduleDelayedRepeatingTask($remove, 20, 1);
      $add = new SendTextsTask($ent, $to);
      $core->getScheduler()->scheduleDelayedRepeatingTask($add, 20, 1);
    }
  }

  public function onSendPacket(DataPacketSendEvent $ev): void {
    $pk = $ev->getPacket();
    if ($pk->pid() === ProtocolInfo::AVAILABLE_COMMANDS_PACKET) {
      /** @var AvailableCommandsPacket $pk */
      if (isset($pk->commandData[TxtCommand::NAME])) {
        $p = $ev->getPlayer();
        $txt = $pk->commandData[TxtCommand::NAME];
        $txt->commandDescription = Lang::fromLocale($p->getLocale())->translateString("command.txt.description");
      }
    }
  }
}