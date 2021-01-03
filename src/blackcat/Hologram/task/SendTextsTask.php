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

namespace blackcat\Hologram\task;

use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use blackcat\Hologram\Core;
use blackcat\Hologram\text\Text;
use blackcat\Hologram\HologramApi;

/**
 * Class SendTextsTask
 * @package blackcat\Hologram\task
 */
class SendTextsTask extends Task {

  /** @var Player */
  private $target;
  /** @var int */
  private $type;

  /** @var array */
  private $ufts;
  /** @var int */
  private $uftsKey = 0;
  private $uftsKeyMax;
  /** @var array */
  private $fts;
  /** @var int */
  private $ftsKey = 0;
  private $ftsKeyMax;

  public function __construct(Player $target, Level $sendTo, int $type = Text::SEND_TYPE_ADD) {
    $this->target = $target;
    $this->type = $type;
    $this->ufts = array_values(HologramApi::getUftsByLevel($sendTo));
    $this->uftsKeyMax = count($this->ufts);
    $this->fts = array_values(HologramApi::getFtsByLevel($sendTo));
    $this->ftsKeyMax = count($this->fts);
  }

  public function onRun(int $currentTick) {
    if ($this->uftsKey === $this->uftsKeyMax) {
      if ($this->ftsKey === $this->ftsKeyMax) {
        $this->onSuccess();
      }else {
        $ft = $this->fts[$this->ftsKey];
        $ft->sendToPlayer($this->target, $this->type);
        ++$this->ftsKey;
      }
    }else {
      $uft = $this->ufts[$this->uftsKey];
      $uft->sendToPlayer($this->target, $this->type);
      ++$this->uftsKey;
    }
  }

  private function onSuccess(): void {
    Core::get()->getScheduler()->cancelTask($this->getTaskId());
  }
}