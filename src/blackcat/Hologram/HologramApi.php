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

use pocketmine\level\Level;
use pocketmine\Server;
use blackcat\Hologram\data\FloatingTextData;
use blackcat\Hologram\text\FloatingText;
use blackcat\Hologram\text\Text;
use blackcat\Hologram\text\UnremovableFloatingText;

/**
 * Class HologramApi (Based on TexterAPI)
 * @package blackcat\Hologram
 */
class HologramApi {

  /** @var array */
  private static $ufts = [];
  /** @var array */
  private static $fts = [];

  private function __construct() {
    // DO NOT USE THIS METHOD!
  }

  /**
   * Register text in the Hologram plugin and enable management using HologramApi
   * If you don't do this registration,
   * you need to manage FloatingText manually with the Text class.
   * @link blackcat\Hologram\text\{Text, FloatingText}
   * @param Text $text
   */
  public static function registerText(Text $text): void {
    switch (true) {
      case $text instanceof UnremovableFloatingText:
        self::$ufts[$text->getLevel()->getFolderName()][$text->getName()] = $text;
        break;

      case $text instanceof FloatingText:
        self::$fts[$text->getLevel()->getFolderName()][$text->getName()] = $text;
        FloatingTextData::make()->saveFtChange($text);
        break;
    }
  }

  /**
   * @return array[string FolderName][string TextName] = UnremovableFloatingText
   */
  public static function getUfts(): array {
    return self::$ufts;
  }

  /**
   * @param Level $level
   * @return array[string FolderName][string TextName] = UnremovableFloatingText
   */
  public static function getUftsByLevel(Level $level): array {
    return self::getUftsByLevelName($level->getFolderName());
  }

  /**
   * @param string $levelName
   * @return array[string FolderName][string TextName] = UnremovableFloatingText
   */
  public static function getUftsByLevelName(string $levelName): array {
    return self::$ufts[$levelName] ?? [];
  }

  /**
   * @param Level $level
   * @param string $name
   * @return null|UnremovableFloatingText
   */
  public static function getUftByLevel(Level $level, string $name): ?UnremovableFloatingText {
    $ufts = self::getUftsByLevel($level);
    return $ufts[$name] ?? null;
  }

  /**
   * @param string $levelName
   * @param string $name
   * @return null|UnremovableFloatingText
   */
  public static function getUftByLevelName(string $levelName, string $name): ?UnremovableFloatingText {
    $ufts = self::getUftsByLevelName($levelName);
    return $ufts[$name] ?? null;
  }

  /**
   * @return array[string FolderName][string TextName] = FloatingText
   */
  public static function getFts(): array {
    return self::$fts;
  }

  /**
   * @param Level $level
   * @return array[string FolderName][string TextName] = FloatingText
   */
  public static function getFtsByLevel(Level $level): array {
    return self::getFtsByLevelName($level->getFolderName());
  }

  /**
   * @param string $levelName
   * @return array[string FolderName][string TextName] = FloatingText
   */
  public static function getFtsByLevelName(string $levelName): array {
    return self::$fts[$levelName] ?? [];
  }

  /**
   * @param Level $level
   * @param string $name
   * @return null|FloatingText
   */
  public static function getFtByLevel(Level $level, string $name): ?FloatingText {
    $fts = self::getFtsByLevel($level);
    return $fts[$name] ?? null;
  }

  /**
   * @param string $levelName
   * @param string $name
   * @return null|FloatingText
   */
  public static function getFtByLevelName(string $levelName, string $name): ?FloatingText {
    $fts = self::getFtsByLevelName($levelName);
    return $fts[$name] ?? null;
  }

  /**
   * @param Level $level
   * @return bool
   */
  public static function removeFtsByLevel(Level $level): bool {
    $fts = self::getFtsByLevel($level);
    $onLevel = $fts[$level->getFolderName()] ?? [];
    if (!empty($onLevel)) {
      /** @var FloatingText $ft */
      foreach ($onLevel as $ft) {
        $ft->sendToLevel($level, Text::SEND_TYPE_REMOVE);
      }
      FloatingTextData::make()->removeFtsByLevel($level);
      unset(self::$fts[$level->getFolderName()]);
      return true;
    }
    return false;
  }

  /**
   * @param string $levelName
   * @return bool
   */
  public static function removeFtsByLevelName(string $levelName): bool {
    $level = Server::getInstance()->getLevelByName($levelName);
    return isset($level) ? self::removeFtsByLevel($level) : false;
  }

  /**
   * @param Level $level
   * @param string $name
   * @return bool
   */
  public static function removeFtByLevel(Level $level, string $name): bool {
    $ft = self::getFtByLevel($level, $name);
    if (isset($ft)) {
      $ft->sendToLevel($level, Text::SEND_TYPE_REMOVE);
      FloatingTextData::make()->removeFtByLevel($level, $name);
      unset(self::$fts[$level->getFolderName()][$name]);
      return true;
    }
    return false;
  }

  /**
   * @param string $levelName
   * @param string $name
   * @return bool
   */
  public static function removeFtByLevelName(string $levelName, string $name): bool {
    $level = Server::getInstance()->getLevelByName($levelName);
    return isset($level) ? self::removeFtByLevel($level, $name) : false;
  }
}