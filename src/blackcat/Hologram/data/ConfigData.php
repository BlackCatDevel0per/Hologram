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

namespace blackcat\Hologram\data;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use blackcat\Hologram\Core;
use blackcat\Hologram\i18n\Lang;
use function strtolower;
use function mb_strlen;
use function mb_substr_count;
use function is_array;
use function array_flip;
use function is_string;

/**
 * Class ConfigData
 * @package blackcat\Hologram\data
 */
class ConfigData extends Config implements Data {

  /** @var ConfigData */
  private static $instance;

  public function __construct(PluginBase $plugin, string $path, string $file) {
    $plugin->saveResource($file);
    parent::__construct($path.$file, Config::YAML);
    self::$instance = $this;
    $this->checkIsUpdater();
  }

  private function checkIsUpdater() {
    Core::setIsUpdater(!$this->exists("can.use.only.op"));
  }

  public function getLocale(): string {
    return strtolower($this->get("locale", Lang::FALLBACK));
  }

  public function checkUpdate(): bool {
    return (bool) $this->get("check.update", true);
  }

  public function canUseCommands(): bool {
    return (bool) $this->get("can.use.commands", true);
  }

  public function canUseOnlyOp(): bool {
    return (bool) $this->get("can.use.only.op", false);
  }

  public function checkCharLimit(string $text): bool {
    $limit = $this->getCharLimit();
    if ($limit === -1) {
      return true;
    }else {
      $length = mb_strlen($text);
      return $limit >= $length;
    }
  }

  public function getCharLimit(): int {
    return (int) $this->get("char", -1);
  }

  public function checkFeedLimit(string $text): bool {
    $limit = $this->getFeedLimit();
    if ($limit === -1)
      return true;
    $feed = mb_substr_count($text, "#");
    return $limit >= $feed;
  }

  public function getFeedLimit(): int {
    return (int) $this->get("feed", -1);
  }

  public function checkWorldLimit(string $levelName): bool {
    if ($this->exists("world")) {
      $limited = $this->get("world", []);
      if (is_array($limited)) {
        $flip = array_flip($limited);
        return !isset($flip[$levelName]);
      }
      if (is_string($limited)) {
        return $limited !== $levelName;
      }
    }
    return true;// isn't limited
  }

  public static function make(): ConfigData {
    return self::$instance;
  }
}