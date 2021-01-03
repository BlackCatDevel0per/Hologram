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

use pocketmine\level\Position;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use blackcat\Hologram\Core;
use blackcat\Hologram\data\Data;
use blackcat\Hologram\data\FloatingTextData;
use blackcat\Hologram\data\UnremovableFloatingTextData;
use blackcat\Hologram\i18n\Lang;
use blackcat\Hologram\text\FloatingText;
use blackcat\Hologram\text\UnremovableFloatingText;
use blackcat\Hologram\HologramApi;
use function count;

/**
 * Class PrepareTextsTask
 * @package blackcat\Hologram\task
 */
class PrepareTextsTask extends Task {

  /** @var Server */
  private $server;
  /** @var array */
  private $ufts;
  /** @var int */
  private $uftsCount = 0;
  /** @var int */
  private $uftsMax;
  /** @var array */
  private $fts;
  /** @var int */
  private $ftsCount = 0;
  /** @var int */
  private $ftsMax;

  public function __construct() {
    $this->server = Server::getInstance();
    $this->ufts = UnremovableFloatingTextData::make()->getData();
    $this->uftsMax = count($this->ufts);
    $this->fts = FloatingTextData::make()->getData();
    $this->ftsMax = count($this->fts);
  }

  public function onRun(int $tick) {
    if ($this->uftsCount === $this->uftsMax) {
      if ($this->ftsCount === $this->ftsMax) {
        $this->onSuccess();
      }else {
        $data = $this->fts[$this->ftsCount];
        $textName = $data[Data::KEY_NAME];
        $loaded = Server::getInstance()->isLevelLoaded($data[Data::KEY_LEVEL]);
        $canLoad = true;
        if (!$loaded) $canLoad = $this->server->loadLevel($data[Data::KEY_LEVEL]);
        if ($canLoad) {
          $level = $this->server->getLevelByName($data[Data::KEY_LEVEL]);
          if ($level !== null) {
            $x = $data[Data::KEY_X];
            $y = $data[Data::KEY_Y];
            $z = $data[Data::KEY_Z];
            $pos = new Position($x, $y, $z, $level);
            $title = $data[Data::KEY_TITLE];
            $text = $data[Data::KEY_TEXT];
            $owner = $data[FloatingTextData::KEY_OWNER];
            $ft = new FloatingText($textName, $pos, $title, $text, $owner);
            HologramApi::registerText($ft);
          }
        }
        ++$this->ftsCount;
      }
    }else {
      $data = $this->ufts[$this->uftsCount];
      $textName = $data[Data::KEY_NAME];
      $loaded = $this->server->isLevelLoaded($data[Data::KEY_LEVEL]);
      $canLoad = true;
      if (!$loaded) $canLoad = $this->server->loadLevel($data[Data::KEY_LEVEL]);
      if ($canLoad) {
        $level = $this->server->getLevelByName($data[Data::KEY_LEVEL]);
        if ($level !== null) {
          $x = $data[Data::KEY_X];
          $y = $data[Data::KEY_Y];
          $z = $data[Data::KEY_Z];
          $pos = new Position($x, $y, $z, $level);
          $title = $data[Data::KEY_TITLE];
          $text = $data[Data::KEY_TEXT];
          $uft = new UnremovableFloatingText($textName, $pos, $title, $text);
          HologramApi::registerText($uft);
        }
      }
      ++$this->uftsCount;
    }
  }

  private function onSuccess(): void {
    $plugin = $this->server->getPluginManager()->getPlugin("Hologram");
    if ($plugin !== null && $plugin->isEnabled()) {
      $message = Lang::fromConsole()->translateString("on.enable.prepared", [
        count(HologramApi::getUfts(), COUNT_RECURSIVE) - count(HologramApi::getUfts()),
        count(HologramApi::getFts(), COUNT_RECURSIVE) - count(HologramApi::getFts())
      ]);
      $core = Core::get();
      $core->getLogger()->info(TextFormat::GREEN . $message);
      $core->getScheduler()->cancelTask($this->getTaskId());
    }
  }
}
