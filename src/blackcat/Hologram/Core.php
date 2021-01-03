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

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\utils\VersionString;
use blackcat\Hologram\command\TxtCommand;
use blackcat\Hologram\data\ConfigData;
use blackcat\Hologram\data\FloatingTextData;
use blackcat\Hologram\data\UnremovableFloatingTextData;
use blackcat\Hologram\i18n\Lang;
use blackcat\Hologram\task\CheckUpdateTask;
use blackcat\Hologram\task\PrepareTextsTask;
use function class_exists;

/**
 * Class Core
 * @package blackcat\Hologram
 */
class Core extends PluginBase {

  /** @var Core */
  private static $core;
  /** @var bool */
  private static $isUpdater = false;

  public function onLoad() {
    self::$core = $this;
    $this
      ->loadResources()
      ->loadLanguage()
      ->registerCommands()
      ->prepareTexts()
      ->checkUpdate();
  }

  public function onEnable() {
      $this->getLogger()->info("§2Плагин [Hologram] Запущен! §1https://vk.com/native_dev");
      $listener = new EventListener;
      $this->getServer()->getPluginManager()->registerEvents($listener, $this);
  }

  private function loadResources(): self {
    $dir = $this->getDataFolder();
    new ConfigData($this, $dir, "config.yml");
    new UnremovableFloatingTextData($this, $dir, "uft.json");
    new FloatingTextData($this, $dir, "ft.json");
    return $this;
  }

  private function loadLanguage(): self {
    new Lang($this);
    $cl = Lang::fromConsole();
    $message1 = $cl->translateString("language.selected", [
      $cl->getName(),
      $cl->getLang()
    ]);
    $this->getLogger()->info(TextFormat::GREEN . $message1);
    if (self::isUpdater()) {
      $message2 = $cl->translateString("on.load.is.updater");
      $this->getLogger()->notice($message2);
    }
    return $this;
  }

  private function registerCommands(): self {
    if ($canUse = ConfigData::make()->canUseCommands()) {
      $map = $this->getServer()->getCommandMap();
      $commands = [
        new TxtCommand($this),
      ];
      $map->registerAll($this->getName(), $commands);
      $message = Lang::fromConsole()->translateString("on.load.commands.on");
    }else {
      $message = Lang::fromConsole()->translateString("on.load.commands.off");
    }
    $this->getLogger()->info(($canUse ? TextFormat::GREEN : TextFormat::RED) . $message);
    return $this;
  }

  private function prepareTexts(): self {
    $prepare = new PrepareTextsTask;
    $this->getScheduler()->scheduleDelayedRepeatingTask($prepare, 20, 1);
    return $this;
  }

  private function checkUpdate(): self {
    if (ConfigData::make()->checkUpdate()) {
      try {
        $this->getServer()->getAsyncPool()->submitTask(new CheckUpdateTask);
      } catch (\Exception $ex) {
        $this->getLogger()->warning($ex->getMessage());
      }
    }
    return $this;
  }

  public function compareVersion(bool $success, ?VersionString $new = null, string $url = "") {
    $cl = Lang::fromConsole();
    $logger = $this->getLogger();
    if ($success) {
      $current = new VersionString($this->getDescription()->getVersion());
      switch ($current->compare($new)) {
        case -1:// new: older
          $message = $cl->translateString("on.load.version.dev");
          $logger->warning($message);
          break;

        case 0:// same
          $message = $cl->translateString("on.load.update.nothing", [
            $current->getFullVersion()
          ]);
          $logger->notice($message);
          break;

        case 1:// new: newer
          $messages[] = $cl->translateString("on.load.update.available.1", [
            $new->getFullVersion(),
            $current->getFullVersion()
          ]);
          $messages[] = $cl->translateString("on.load.update.available.2");
          $messages[] = $cl->translateString("on.load.update.available.3", [
            $url
          ]);
          foreach ($messages as $message) $logger->notice($message);
      }
    }else {
      $message = $cl->translateString("on.load.update.offline");
      $logger->notice($message);
    }
  }

  public static function isUpdater(): bool {
    return self::$isUpdater;
  }

  public static function setIsUpdater(bool $bool = true) {
    self::$isUpdater = $bool;
  }

  public static function get(): Core {
    return self::$core;
  }
}